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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessUpdateQueueCommand extends ServiceAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('advanced-object-search:process-update-queue')
            ->setDescription('processes whole update queue of es search index')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = 1;

        while ($count) {
            $count = $this->service->processUpdateQueue();
        }

        return Command::SUCCESS;
    }
}
