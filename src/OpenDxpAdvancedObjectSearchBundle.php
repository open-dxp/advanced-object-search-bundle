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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle;

use OpenDxp\Bundle\AdvancedObjectSearchBundle\DependencyInjection\OpenDxpAdvancedObjectSearchExtension;
use OpenDxp\Bundle\ElasticsearchClientBundle\OpenDxpElasticsearchClientBundle;
use OpenDxp\Bundle\OpenSearchClientBundle\OpenDxpOpenSearchClientBundle;
use OpenDxp\Bundle\SimpleBackendSearchBundle\OpenDxpSimpleBackendSearchBundle;
use OpenDxp\Extension\Bundle\AbstractOpenDxpBundle;
use OpenDxp\Extension\Bundle\OpenDxpBundleAdminClassicInterface;
use OpenDxp\Extension\Bundle\Traits\BundleAdminClassicTrait;
use OpenDxp\Extension\Bundle\Traits\PackageVersionTrait;
use OpenDxp\HttpKernel\Bundle\DependentBundleInterface;
use OpenDxp\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class OpenDxpAdvancedObjectSearchBundle extends AbstractOpenDxpBundle implements DependentBundleInterface, OpenDxpBundleAdminClassicInterface
{
    use PackageVersionTrait;
    use BundleAdminClassicTrait;

    #[\Override]
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new OpenDxpAdvancedObjectSearchExtension();
        }

        return $this->extension;
    }

    protected function getComposerPackageName(): string
    {
        return 'open-dxp/advanced-object-search-bundle';
    }

    public function getCssPaths(): array
    {
        return [
            '/bundles/opendxpadvancedobjectsearch/css/admin.css',
        ];
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/opendxpadvancedobjectsearch/js/startup.js',
            '/bundles/opendxpadvancedobjectsearch/js/events.js',
            '/bundles/opendxpadvancedobjectsearch/js/selector.js',
            '/bundles/opendxpadvancedobjectsearch/js/helper.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfigPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/conditionPanelContainerBuilder.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/conditionPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/resultAbstractPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/resultPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/resultExtension.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/conditionAbstractPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/conditionEntryPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/conditionGroupPanel.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/default.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/localizedfields.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/numeric.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/manyToManyOne.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/manyToManyObjectRelation.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/manyToManyRelation.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/fieldcollections.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/objectbricks.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/advancedManyToManyObjectRelation.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/advancedManyToManyRelation.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/checkbox.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/select.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/language.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/country.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/user.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/multiselect.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/countrymultiselect.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/languagemultiselect.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/datetime.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/date.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/time.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/quantityValue.js',
            '/bundles/opendxpadvancedobjectsearch/js/searchConfig/fieldConditionPanel/table.js',
            '/bundles/opendxpadvancedobjectsearch/js/portlet/advancedObjectSearch.js',
        ];
    }

    public function getInstaller(): Installer
    {
        return $this->container->get(Installer::class);
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new OpenDxpElasticsearchClientBundle());
        $collection->addBundle(new OpenDxpOpenSearchClientBundle());
        $collection->addBundle(new OpenDxpSimpleBackendSearchBundle());
    }
}
