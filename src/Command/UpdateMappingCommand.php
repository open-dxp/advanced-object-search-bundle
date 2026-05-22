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

use OpenDxp\Model\DataObject\ClassDefinition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMappingCommand extends ServiceAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('advanced-object-search:update-mapping')
            ->setDescription('Deletes and recreates mapping of given classes. Resets update queue for given class.')
            ->addOption('classes', 'c', InputOption::VALUE_OPTIONAL, 'just update specific classes, use "," (comma) to execute more than one class')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classes = [];

        if ($input->getOption('classes')) {
            $classNames = explode(',', (string) $input->getOption('classes'));
            foreach ($classNames as $name) {
                $classes[] = ClassDefinition::getByName($name);
            }
        } else {
            $classes = new ClassDefinition\Listing();
            $classes->load();
            $classes = $classes->getClasses();
        }

        $classes = array_filter($classes);

        foreach ($classes as $class) {
            $indexName = $this->service->getIndexName($class->getName());

            $this->output->writeln('Processing ' . $class->getName() . " -> index $indexName");

            $this->service->updateMapping($class);
        }

        return Command::SUCCESS;
    }
}
