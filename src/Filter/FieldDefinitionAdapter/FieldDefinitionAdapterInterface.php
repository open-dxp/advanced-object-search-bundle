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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldDefinitionAdapter;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Filter\FieldSelectionInformation;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Localization\LocaleServiceInterface;
use OpenDxp\Model\DataObject\ClassDefinition\Data;
use OpenDxp\Model\DataObject\Concrete;

interface FieldDefinitionAdapterInterface
{
    const INDEX_MAPPING_PROPERTY_STANDARD = 'standard';

    const INDEX_MAPPING_PROPERTY_NOT_INHERITED = 'notInherited';

    public function __construct(Service $service, LocaleServiceInterface $locale);

    public function setFieldDefinition(Data $fieldDefinition);

    public function setConsiderInheritance(bool $considerInheritance);

    /**
     * @return array
     */
    public function getESMapping();

    /**
     * @param Concrete $object
     *
     * @return array
     */
    public function getIndexData($object);

    /**
     * @param array|string|int|bool $fieldFilter - see concrete implementations for format
     * @param string $path - sub path for nested objects (only needed internally)
     * @param bool $ignoreInheritance - if true inheritance is not considered during query
     *
     * @return BuilderInterface
     */
    public function getQueryPart($fieldFilter, $ignoreInheritance = false, $path = '');

    /**
     * @param mixed $fieldFilter - see concrete implementations for format
     * @param bool $ignoreInheritance - if true inheritance is not considered during query
     * @param string $path - sub path for nested objects (only needed internally)
     *
     * @return ExistsQuery
     */
    public function getExistsFilter($fieldFilter, $ignoreInheritance = false, $path = '');

    /**
     * returns selectable fields with their type information for search frontend
     *
     * @return FieldSelectionInformation[]
     */
    public function getFieldSelectionInformation();
}
