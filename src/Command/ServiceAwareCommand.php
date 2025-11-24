<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Command;

use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Console\AbstractCommand;
use Symfony\Contracts\Service\Attribute\Required;

abstract class ServiceAwareCommand extends AbstractCommand
{
    protected Service $service;

    public function getService(): Service
    {
        return $this->service;
    }

    #[Required]
    public function setService(Service $service): void
    {
        $this->service = $service;
    }
}
