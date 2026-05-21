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

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldSelectionInformation;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FilterEntry;

class ManyToManyObjectRelation extends ManyToOneRelation implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'manyToManyObjectRelation';

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
        $allowedTypes[] = ['object', 'object_ids'];
        $allowedTypes[] = ['object_filter', 'object_filter'];

        foreach ($this->fieldDefinition->getClasses() as $class) {
            $allowedClasses[] = $class['classes'];
        }

        return [new FieldSelectionInformation(
            $this->fieldDefinition->getName(),
            $this->fieldDefinition->getTitle(),
            $this->fieldType,
            [
                'operators' => [BoolQuery::MUST, BoolQuery::SHOULD, BoolQuery::MUST_NOT, FilterEntry::EXISTS, FilterEntry::NOT_EXISTS],
                'allowedTypes' => $allowedTypes,
                'allowedClasses' => $allowedClasses,
            ]
        )];
    }

    #[\Override]
    protected function doGetIndexDataValue($object, $ignoreInheritance = false)
    {
        $value = parent::doGetIndexDataValue($object, $ignoreInheritance);

        //rewrite all types to 'object' since 'variants' are not supported yet.
        $filteredValues = array_map(function ($item) {
            if (isset($item['element'])) {
                return [
                    'id' => $item['element']['id'],
                    'type' => 'object',
                ];
            } else {
                return [
                    'id' => $item['id'],
                    'type' => 'object',
                ];
            }
        }, $value);

        return $filteredValues;
    }
}
