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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Event;

final class AdvancedObjectSearchEvents
{
    /**
     * @Event("AdvancedObjectSearchBundle\Event\SearchEvent")
     */
    public const string SEARCH_FILTER = 'advanced_object_search.search_filter';

    /**
     * @Event("AdvancedObjectSearchBundle\Event\SearchEvent")
     */
    public const string LISTING_FILER = 'advanced_object_search.listing_filter';
}
