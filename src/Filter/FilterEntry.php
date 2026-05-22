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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use stdClass;

class FilterEntry
{
    public const string EXISTS = 'exists';

    public const string NOT_EXISTS = 'not_exists';

    public const string FIELDNAME_GROUP = '~~group~~';

    protected $operator = BoolQuery::MUST;

    /**
     * @param string $fieldname
     * @param BuilderInterface|string|stdClass|array $filterEntryData
     * @param string $operator
     * @param bool $ignoreInheritance
     */
    public function __construct(
        protected $fieldname,
        protected $filterEntryData,
        $operator = BoolQuery::MUST,
        protected $ignoreInheritance = false
    ) {
        $this->operator = $operator ?: BoolQuery::MUST;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    public function getOuterOperator()
    {
        if ($this->operator == self::EXISTS) {
            return BoolQuery::MUST;
        }

        if ($this->operator == self::NOT_EXISTS) {
            return BoolQuery::MUST_NOT;
        }

        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getFieldname()
    {
        return $this->fieldname;
    }

    /**
     * @param string $fieldname
     */
    public function setFieldname($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    /**
     * @return stdClass|BuilderInterface|string|array
     */
    public function getFilterEntryData()
    {
        return $this->filterEntryData;
    }

    /**
     * @param stdClass|BuilderInterface|string|array $filterEntryData
     */
    public function setFilterEntryData($filterEntryData)
    {
        $this->filterEntryData = $filterEntryData;
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return $this->fieldname == self::FIELDNAME_GROUP;
    }

    /**
     * @return bool
     */
    public function getIgnoreInheritance()
    {
        return $this->ignoreInheritance;
    }
}
