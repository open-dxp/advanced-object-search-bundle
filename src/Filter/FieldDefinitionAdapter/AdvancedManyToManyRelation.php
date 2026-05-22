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

use Override;

class AdvancedManyToManyRelation extends ManyToOneRelation implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'advancedManyToManyRelation';

    #[Override]
    protected function doGetIndexDataValue($object, $ignoreInheritance = false)
    {
        $value = parent::doGetIndexDataValue($object, $ignoreInheritance);

        $filteredValues = array_map(fn ($item) => $item['element'] ?? $item, $value);

        return $filteredValues;
    }
}
