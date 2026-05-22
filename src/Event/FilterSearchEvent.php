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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Event;

use ONGR\ElasticsearchDSL\Search;
use Symfony\Contracts\EventDispatcher\Event;

class FilterSearchEvent extends Event
{
    /**
     * @var Search
     */
    protected $search;

    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    public function getSearch(): Search
    {
        return $this->search;
    }
}
