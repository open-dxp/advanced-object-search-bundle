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
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldSelectionInformation;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\Concrete;
use Override;

class Checkbox extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'checkbox';

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
                            'type' => 'boolean',
                        ],
                        self::INDEX_MAPPING_PROPERTY_NOT_INHERITED => [
                            'type' => 'boolean',
                        ],
                    ],
                ],
            ];
        }

        return [
            $this->fieldDefinition->getName(),
            [
                'type' => 'boolean',
            ],
        ];
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

        return (bool) $value;
    }

    /**
     * @param Concrete $object
     *
     * @return mixed
     */
    #[Override]
    public function getIndexData($object)
    {
        $value = $this->doGetIndexDataValue($object, false);

        if ($this->considerInheritance) {
            $notInheritedValue = $this->doGetIndexDataValue($object, true);

            $returnValue = [];
            $returnValue[self::INDEX_MAPPING_PROPERTY_STANDARD] = $value;
            $returnValue[self::INDEX_MAPPING_PROPERTY_NOT_INHERITED] = $notInheritedValue;

            return $returnValue;
        } else {
            return $value;
        }
    }

    /**
     * @param bool|string $fieldFilter
     *
     * filter field format as follows:
     *   - simple boolean like
     *       true | false  --> creates QueryStringQuery
     * @param bool $ignoreInheritance
     * @param string $path
     *
     * @return BuilderInterface
     */
    #[Override]
    public function getQueryPart($fieldFilter, $ignoreInheritance = false, $path = '')
    {
        return new TermQuery($path . $this->fieldDefinition->getName() . $this->buildQueryFieldPostfix($ignoreInheritance), $fieldFilter);
    }

    #[Override]
    public function getFieldSelectionInformation()
    {
        return [new FieldSelectionInformation(
            $this->fieldDefinition->getName(),
            $this->fieldDefinition->getTitle(),
            $this->fieldType,
            [
                'operators' => [BoolQuery::MUST, BoolQuery::MUST_NOT],
                'classInheritanceEnabled' => $this->considerInheritance,
            ]
        )];
    }
}
