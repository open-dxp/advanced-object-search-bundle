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
