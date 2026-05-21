<?php

/**
 * OpenDXP
 *
 * This source file is licensed under the GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (https://pimcore.com)
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.ch)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldDefinitionAdapter;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldSelectionInformation;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FilterEntry;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\Concrete;
use OpenDxp\Normalizer\NormalizerInterface;

/**
 * @property \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fieldDefinition
 */
class ManyToOneRelation extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'manyToOneRelation';

    /**
     * @return array
     */
    #[\Override]
    public function getESMapping()
    {
        if ($this->considerInheritance) {
            return [
                $this->fieldDefinition->getName(),
                [
                    'properties' => [
                        self::INDEX_MAPPING_PROPERTY_STANDARD => [
                            'type' => 'nested',
                            'properties' => [
                                'type' => ['type' => 'keyword'],
                                'id' => ['type' => 'long'],
                            ],
                        ],
                        self::INDEX_MAPPING_PROPERTY_NOT_INHERITED => [
                            'type' => 'nested',
                            'properties' => [
                                'type' => ['type' => 'keyword'],
                                'id' => ['type' => 'long'],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            return [
                $this->fieldDefinition->getName(),
                [
                    'type' => 'nested',
                    'properties' => [
                        'type' => ['type' => 'keyword'],
                        'id' => ['type' => 'long'],
                    ],
                ],
            ];
        }
    }

    /**
     * @param array|mixed $fieldFilter
     *
     * filter field format as follows:
     *   - simple array like
     *      [
     *          'type' => 'object|asset|document'
     *          'id'   => 3242  ( or id-array like [3234,2432,24342,35435]
     *      ]
     *      --> creates TermQuery
     *
     *   - array with sub query
     *      [
     *         'type'               => 'object|asset|document'
     *         'classId'            => 'CLASSID' (optional only with type object),
     *         'fulltextSearchTerm' => string as fulltext term
     *         'filters'            => [ STANDARD FULL FEATURED FILTER ARRAY ]
     *      ]
     *       --> creates a sub query with given information, receives ids and then creates TermsQuery
     * @param bool $ignoreInheritance
     * @param string $path
     *
     * @return BuilderInterface
     */
    #[\Override]
    public function getQueryPart($fieldFilter, $ignoreInheritance = false, $path = '')
    {
        if (is_array($fieldFilter)) {
            $path = $path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance);

            $boolQuery = new BoolQuery();

            if (isset($fieldFilter['id']) && $fieldFilter['id']) {
                $idArray = $fieldFilter['id'];
                if (!is_array($idArray)) {
                    $idArray = [$idArray];
                } else {
                    $idArray = array_filter($idArray);
                }
            } elseif ($fieldFilter['type'] == 'object' && $fieldFilter['classId'] && ($fieldFilter['filters'] || $fieldFilter['fulltextSearchTerm'])) {
                $results = $this->service->doFilter($fieldFilter['classId'], $fieldFilter['filters'], $fieldFilter['fulltextSearchTerm']);
                $idArray = $this->service->extractIdsFromResult($results);
            } else {
                throw new \Exception('invalid filter entry definition ' . print_r($fieldFilter, true));
            }

            if ($idArray) {
                foreach ($idArray as $id) {
                    $innerBoolQuery = new BoolQuery();
                    $innerBoolQuery->add(new TermQuery($path . '.type', $fieldFilter['type']));
                    $innerBoolQuery->add(new TermQuery($path . '.id', $id));

                    $boolQuery->add(new NestedQuery($path, $innerBoolQuery), BoolQuery::SHOULD);
                }
                $boolQuery->addParameter('minimum_should_match', '1');
            } else {
                $boolQuery->add(new ExistsQuery($path . '.notavailablefield'));
            }

            return $boolQuery;
        } else {
            throw new \Exception('invalid filter entry for relations filter: ' . print_r($fieldFilter, true));
        }
    }

    /**
     * returns selectable fields with their type information for search frontend
     *
     * @return FieldSelectionInformation[]
     */
    #[\Override]
    public function getFieldSelectionInformation()
    {
        $allowedTypes = [];
        $allowedClasses = [];
        if ($this->fieldDefinition->getAssetsAllowed()) {
            $allowedTypes[] = ['asset', 'asset_ids'];
        }
        if ($this->fieldDefinition->getDocumentsAllowed()) {
            $allowedTypes[] = ['document', 'document_ids'];
        }
        if ($this->fieldDefinition->getObjectsAllowed()) {
            $allowedTypes[] = ['object', 'object_ids'];
            $allowedTypes[] = ['object_filter', 'object_filter'];

            foreach ($this->fieldDefinition->getClasses() as $class) {
                $allowedClasses[] = $class['classes'];
            }
        }

        return [new FieldSelectionInformation(
            $this->fieldDefinition->getName(),
            $this->fieldDefinition->getTitle(),
            $this->fieldType,
            [
                'operators' => [BoolQuery::MUST, BoolQuery::SHOULD, BoolQuery::MUST_NOT, FilterEntry::EXISTS, FilterEntry::NOT_EXISTS],
                'allowedTypes' => $allowedTypes,
                'allowedClasses' => $allowedClasses,
                'classInheritanceEnabled' => $this->considerInheritance,
            ]
        )];
    }

    /**
     * @param Concrete $object
     * @param bool $ignoreInheritance
     */
    #[\Override]
    protected function doGetIndexDataValue($object, $ignoreInheritance = false)
    {
        $inheritanceBackup = null;
        if ($ignoreInheritance) {
            $inheritanceBackup = AbstractObject::getGetInheritedValues();
            AbstractObject::setGetInheritedValues(false);
        }

        $rawValue = $this->loadRawDataFromContainer($object, $this->fieldDefinition->getName());
        $value = null;
        if ($this->fieldDefinition instanceof NormalizerInterface) {
            $value = $this->fieldDefinition->normalize($rawValue);
        }

        if ($ignoreInheritance) {
            AbstractObject::setGetInheritedValues($inheritanceBackup);
        }

        return $value;
    }
}
