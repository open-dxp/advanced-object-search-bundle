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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Model\SavedSearch;

use OpenDxp\Bundle\AdvancedObjectSearchBundle\Model\SavedSearch;
use OpenDxp\Model;
use Override;

/**
 * @method SavedSearch[] load()
 * @method int getTotalCount()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * Contains the results of the list. They are all an instance of SavedSearch
     *
     * @var array
     */
    public $savedSearches = [];

    /**
     * Tests if the given key is an valid order key to sort the results
     */
    #[Override]
    public function isValidOrderKey($key): bool
    {
        return true;
    }

    /**
     * @param array $savedSearches
     *
     * @return $this
     */
    public function setSavedSearches($savedSearches)
    {
        $this->savedSearches = $savedSearches;

        return $this;
    }

    /**
     * @return array
     */
    public function getSavedSearches()
    {
        return $this->savedSearches;
    }
}
