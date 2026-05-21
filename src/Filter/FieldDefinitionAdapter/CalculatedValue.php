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

use OpenDxp\Model\DataObject\Concrete;

class CalculatedValue extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @param Concrete $object
     * @param bool $ignoreInheritance
     *
     * @return string
     */
    #[\Override]
    protected function doGetIndexDataValue($object, $ignoreInheritance = false)
    {
        $name = $this->fieldDefinition->getName();
        $value = $this->loadRawDataFromContainer($object, $name);

        return (string) $value;
    }
}
