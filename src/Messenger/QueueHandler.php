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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Messenger;

use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Model\Tool\TmpStore;
use Symfony\Component\Messenger\MessageBusInterface;

class QueueHandler
{
    const IMPORTER_WORKER_COUNT_TMP_STORE_KEY = 'ADVANCED-OBJECT-SEARCH::worker-count';

    public function __construct(
        protected Service $queueService,
        protected MessageBusInterface $messageBus,
        protected int $workerCountLifeTime,
        protected int $workerItemCount,
        protected int $workerCount
    ) {
    }

    public function __invoke(QueueMessage $message)
    {
        $this->queueService->doProcessUpdateQueue($message->getWorkerId(), $message->getEntries());

        $this->removeMessage($message->getWorkerId());
        $this->dispatchMessages();
    }

    public function dispatchMessages()
    {
        $dispatchedMessageCount = $this->getMessageCount();

        $addWorkers = true;
        while ($addWorkers && $dispatchedMessageCount < $this->workerCount) {
            $workerId = uniqid();
            $entries = $this->queueService->initUpdateQueue($workerId, $this->workerItemCount);
            if (!empty($entries)) {
                $this->addMessage($workerId);
                $this->messageBus->dispatch(new QueueMessage($workerId, $entries));
                $dispatchedMessageCount = $this->getMessageCount();
            } else {
                $addWorkers = false;
            }
        }
    }

    private function addMessage(string $messageId)
    {
        TmpStore::set(self::IMPORTER_WORKER_COUNT_TMP_STORE_KEY . $messageId, true, self::IMPORTER_WORKER_COUNT_TMP_STORE_KEY, $this->workerCountLifeTime);
    }

    private function removeMessage(string $messageId)
    {
        TmpStore::delete(self::IMPORTER_WORKER_COUNT_TMP_STORE_KEY . $messageId);
    }

    private function getMessageCount(): int
    {
        $ids = TmpStore::getIdsByTag(self::IMPORTER_WORKER_COUNT_TMP_STORE_KEY);
        $runningWorkers = [];
        foreach ($ids as $id) {
            $runningWorkers[] = TmpStore::get($id);
        }

        return count(array_filter($runningWorkers));
    }
}
