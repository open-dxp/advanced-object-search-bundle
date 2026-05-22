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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\EventListener;

use Exception;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Event\Model\DataObject\ClassDefinitionEvent;
use OpenDxp\Event\Model\DataObjectEvent;
use OpenDxp\Logger;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\Concrete;

class IndexUpdateListener
{
    /**
     * @var Service
     */
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function updateObject(DataObjectEvent $event)
    {
        //do not update index when auto save or only saving version
        if (
            ($event->hasArgument('isAutoSave') && $event->getArgument('isAutoSave')) ||
            ($event->hasArgument('saveVersionOnly') && $event->getArgument('saveVersionOnly'))
        ) {
            return;
        }

        $inheritanceBackup = AbstractObject::getGetInheritedValues();
        AbstractObject::setGetInheritedValues(true);

        $object = $event->getObject();
        if ($object instanceof Concrete) {
            $this->service->doUpdateIndexData($object);
        }

        AbstractObject::setGetInheritedValues($inheritanceBackup);
    }

    public function deleteObject(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Concrete) {
            $this->service->doDeleteFromIndex($object);
        }
    }

    public function updateMapping(ClassDefinitionEvent $event)
    {
        $classDefinition = $event->getClassDefinition();
        $this->service->updateMapping($classDefinition);
    }

    public function deleteIndex(ClassDefinitionEvent $event)
    {
        $classDefinition = $event->getClassDefinition();

        try {
            $this->service->deleteIndex($classDefinition);
        } catch (Exception $e) {
            Logger::err($e);
        }
    }
}
