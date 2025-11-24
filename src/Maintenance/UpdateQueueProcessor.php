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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Maintenance;

use OpenDxp\Bundle\AdvancedObjectSearchBundle\Messenger\QueueHandler;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Maintenance\TaskInterface;

class UpdateQueueProcessor implements TaskInterface
{
    protected Service $service;

    protected bool $messengerQueueActivated;

    protected QueueHandler $queueHandler;

    public function __construct(Service $service, bool $messengerQueueActivated, QueueHandler $queueHandler)
    {
        $this->service = $service;
        $this->messengerQueueActivated = $messengerQueueActivated;
        $this->queueHandler = $queueHandler;
    }

    public function execute(): void
    {
        if ($this->messengerQueueActivated) {
            $this->queueHandler->dispatchMessages();
        } else {
            $this->service->processUpdateQueue(500);
        }
    }
}
