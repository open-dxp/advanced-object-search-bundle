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
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldDefinitionAdapter;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldSelectionInformation;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FilterEntry;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\Concrete;
use Override;

class Numeric extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'numeric';

    /**
     * @return array
     */
    #[Override]
    public function getESMapping()
    {
        if ($this->considerInheritance) {
            return [
                $this->fieldDefinition->getName(),
                [
                    'properties' => [
                        self::INDEX_MAPPING_PROPERTY_STANDARD => [
                            'type' => 'float',
                        ],
                        self::INDEX_MAPPING_PROPERTY_NOT_INHERITED => [
                            'type' => 'float',
                        ],
                    ],
                ],
            ];
        } else {
            return [
                $this->fieldDefinition->getName(),
                [
                    'type' => 'float',
                ],
            ];
        }
    }

    /**
     * @param array|mixed $fieldFilter
     *
     * filter field format as follows:
     *   - simple number like
     *       234.54   --> creates TermQuery
     *   - array with gt, gte, lt, lte like
     *      ["gte" => 40, "lte" => 45] --> creates RangeQuery
     * @param bool $ignoreInheritance
     * @param string $path
     *
     * @return BuilderInterface
     */
    #[Override]
    public function getQueryPart($fieldFilter, $ignoreInheritance = false, $path = '')
    {
        if (is_array($fieldFilter)) {
            return new RangeQuery($path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance), $fieldFilter);
        } else {
            return new TermQuery($path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance), $fieldFilter);
        }
    }

    /**
     * returns selectable fields with their type information for search frontend
     *
     * @return FieldSelectionInformation[]
     */
    #[Override]
    public function getFieldSelectionInformation()
    {
        return [new FieldSelectionInformation(
            $this->fieldDefinition->getName(),
            $this->fieldDefinition->getTitle(),
            $this->fieldType,
            [
                'operators' => ['lt', 'lte', 'eq', 'gte', 'gt', FilterEntry::EXISTS, FilterEntry::NOT_EXISTS ],
                'classInheritanceEnabled' => $this->considerInheritance,
            ]
        )];
    }

    /**
     * @param Concrete $object
     * @param bool $ignoreInheritance
     */
    #[Override]
    protected function doGetIndexDataValue($object, $ignoreInheritance = false)
    {
        $inheritanceBackup = null;
        if ($ignoreInheritance) {
            $inheritanceBackup = AbstractObject::getGetInheritedValues();
            AbstractObject::setGetInheritedValues(false);
        }

        $value = $this->loadRawDataFromContainer($object, $this->fieldDefinition->getName());

        if ($ignoreInheritance) {
            AbstractObject::setGetInheritedValues($inheritanceBackup);
        }

        return $value;
    }
}
