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
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldSelectionInformation;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FilterEntry;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\Concrete;

/**
 * @property \OpenDxp\Model\DataObject\ClassDefinition\Data\QuantityValue $fieldDefinition
 */
class QuantityValue extends Numeric implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'quantityValue';

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
                            'properties' => [
                                'value' => [
                                    'type' => 'float',
                                ],
                                'unit' => [
                                    'type' => 'keyword',
                                ],
                            ],
                        ],
                        self::INDEX_MAPPING_PROPERTY_NOT_INHERITED => [
                            'properties' => [
                                'value' => [
                                    'type' => 'float',
                                ],
                                'unit' => [
                                    'type' => 'keyword',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            return [
                $this->fieldDefinition->getName(),
                [
                    'properties' => [
                        'value' => [
                            'type' => 'float',
                        ],
                        'unit' => [
                            'type' => 'keyword',
                        ],
                    ],

                ],
            ];
        }
    }

    /**
     * @param array $fieldFilter
     *
     * filter field format as follows:
     *   - simple array with number/unitID like
     *       ["value" => 234.54, "unit" => 3]   --> creates TermQuery
     *   - array with gt, gte, lt, lte like
     *      ["value" => ["gte" => 40, "lte" => 45], "unit" => 3] --> creates RangeQuery
     * @param bool $ignoreInheritance
     * @param string $path
     *
     * @return BuilderInterface
     */
    #[\Override]
    public function getQueryPart($fieldFilter, $ignoreInheritance = false, $path = '')
    {
        $boolQuery = new BoolQuery();
        if (is_array($fieldFilter) && is_array($fieldFilter['value'])) {
            $boolQuery->add(new RangeQuery($path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance) . '.value', $fieldFilter['value']));
        } else {
            $boolQuery->add(new TermQuery($path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance) . '.value', $fieldFilter['value']));
        }
        $boolQuery->add(new TermQuery($path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance) . '.unit', $fieldFilter['unit']));

        return $boolQuery;
    }

    /**
     * returns selectable fields with their type information for search frontend
     *
     * @return FieldSelectionInformation[]
     */
    #[\Override]
    public function getFieldSelectionInformation()
    {
        return [new FieldSelectionInformation(
            $this->fieldDefinition->getName(),
            $this->fieldDefinition->getTitle(),
            $this->fieldType,
            [
                'operators' => ['lt', 'lte', 'eq', 'gte', 'gt', FilterEntry::EXISTS, FilterEntry::NOT_EXISTS ],
                'classInheritanceEnabled' => $this->considerInheritance,
                'units' => $this->fieldDefinition->getValidUnits(),
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

        $value = null;
        $rawValue = $this->loadRawDataFromContainer($object, $this->fieldDefinition->getName());
        if ($rawValue instanceof \OpenDxp\Model\DataObject\Data\QuantityValue) {
            $value = [
                'value' => $rawValue->getValue(),
                'unit' => $rawValue->getUnitId(),
            ];
        }

        if ($ignoreInheritance) {
            AbstractObject::setGetInheritedValues($inheritanceBackup);
        }

        return $value;
    }
}
