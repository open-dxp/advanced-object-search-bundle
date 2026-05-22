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

use Exception;
use OpenDxp;
use OpenDxp\Model\DataObject\AbstractObject;
use OpenDxp\Model\DataObject\ClassDefinition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexCommand extends ServiceAwareCommand
{
    public function __construct(protected ?array $indexConfiguration)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('advanced-object-search:re-index')
            ->setDescription('Reindex all objects of given class. Does not delete index first or resets update queue.')
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
        $elementsPerLoop = $this->indexConfiguration['elements_per_loop'];

        foreach ($classes as $class) {
            $listClassName = '\\OpenDxp\\Model\\DataObject\\' . ucfirst((string) $class->getName()) . '\\Listing';
            $list = new $listClassName();
            $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
            $list->setUnpublished(true);

            $elementsTotal = $list->getTotalCount();

            for ($i = 0; $i < (ceil($elementsTotal / $elementsPerLoop)); $i++) {
                $list->setLimit($elementsPerLoop);
                $list->setOffset($i * $elementsPerLoop);

                $this->output->writeln('Processing ' . $class->getName() . ': ' . min($list->getOffset() + $elementsPerLoop, $elementsTotal) . '/' . $elementsTotal);

                $objects = $list->load();
                foreach ($objects as $object) {
                    try {
                        $this->service->doUpdateIndexData($object, true);
                    } catch (Exception $e) {
                        $this->writeError($e);
                    }
                }
                OpenDxp::collectGarbage();
            }
        }

        return Command::SUCCESS;
    }
}
