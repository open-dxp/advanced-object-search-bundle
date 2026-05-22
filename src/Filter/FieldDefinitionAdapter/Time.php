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

use DateTimeInterface;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\Concrete;
use Override;

class Time extends Datetime implements FieldDefinitionAdapterInterface
{
    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = 'time';

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

        $value = null;

        $getter = 'get' . $this->fieldDefinition->getName();
        $valueObject = $object->$getter();
        if ($valueObject) {
            $valueObject = new \DateTime('0000-01-01T' . $valueObject);
            $value = $valueObject->format(DateTimeInterface::ATOM);
        }

        if ($ignoreInheritance) {
            AbstractObject::setGetInheritedValues($inheritanceBackup);
        }

        return $value;
    }
}
