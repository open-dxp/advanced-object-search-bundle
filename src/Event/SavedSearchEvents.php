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

final class SavedSearchEvents
{
    /**
     * @Event("AdvancedObjectSearchBundle\Event\SavedSearchEvent")
     */
    const string PRE_SAVE = 'advanced_object_search.saved_search.preSave';

    /**
     * @Event("AdvancedObjectSearchBundle\Event\SavedSearchEvent")
     */
    const string POST_SAVE = 'advanced_object_search.saved_search.postSave';

    /**
     * @Event("AdvancedObjectSearchBundle\Event\SavedSearchEvent")
     */
    const string PRE_DELETE = 'advanced_object_search.saved_search.preDelete';

    /**
     * @Event("AdvancedObjectSearchBundle\Event\SavedSearchEvent")
     */
    const string POST_DELETE = 'advanced_object_search.saved_search.postDelete';
}
