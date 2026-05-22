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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Model\SavedSearch\Listing;

use Doctrine\DBAL\Exception;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Model\SavedSearch;
use OpenDxp\Model;

/**
 * @property SavedSearch\Listing $model
 */
class Dao extends Model\Listing\Dao\AbstractDao
{
    /**
     * Loads a list of tags for the specifies parameters, returns an array of Element\Tag elements
     *
     *
     * @throws Exception
     */
    public function load(): array
    {
        $searchIds = $this->db->fetchFirstColumn('SELECT id FROM ' . $this->db->quoteIdentifier(SavedSearch\Dao::TABLE_NAME) . ' ' . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        $searches = [];
        foreach ($searchIds as $id) {
            if ($savedSearch = SavedSearch::getById($id)) {
                $searches[] = $savedSearch;
            }
        }

        $this->model->setSavedSearches($searches);

        return $searches;
    }

    /**
     * @throws Exception
     */
    public function loadIdList()
    {
        return $this->db->fetchFirstColumn('SELECT id FROM ' . $this->db->quoteIdentifier(SavedSearch\Dao::TABLE_NAME) . ' ' . $this->getCondition() . $this->getGroupBy() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());
    }

    public function getTotalCount(): int
    {
        $amount = 0;

        try {
            $amount = (int) $this->db->fetchOne('SELECT COUNT(*) as amount FROM ' . $this->db->quoteIdentifier(SavedSearch\Dao::TABLE_NAME) . ' ' . $this->getCondition(), $this->model->getConditionVariables());
        } catch (\Exception) {
        }

        return $amount;
    }
}
