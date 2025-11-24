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

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\DependencyInjection;

use OpenDxp\Bundle\AdvancedObjectSearchBundle\Enum\ClientType;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Maintenance\UpdateQueueProcessor;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Messenger\QueueHandler;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class OpenDxpAdvancedObjectSearchExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'opendxp_advanced_object_search';
    }

    public function loadInternal(array $config, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        $container->setParameter(
            'advanced_object_search.core_fields_configuration',
            $config['core_fields_configuration']
        );

        // load mappings for field definition adapters
        $serviceLocator = $container->getDefinition('bundle.advanced_object_search.filter_locator');
        $arguments = [];

        foreach ($config['field_definition_adapters'] as $key => $serviceId) {
            $arguments[$key] = new Reference($serviceId);
        }

        $serviceLocator->setArgument(0, $arguments);

        $container->setParameter('opendxp.advanced_object_search.index_name_prefix', $config['index_name_prefix']);

        $container->setParameter(
            'opendxp.advanced_object_search.index_configuration',
            $config['index_configuration']
        );

        $definition = $container->getDefinition(QueueHandler::class);
        $definition->setArgument('$workerCountLifeTime', $config['messenger_queue_processing']['worker_count_lifetime']);
        $definition->setArgument('$workerItemCount', $config['messenger_queue_processing']['worker_item_count']);
        $definition->setArgument('$workerCount', $config['messenger_queue_processing']['worker_count']);

        $definition = $container->getDefinition(UpdateQueueProcessor::class);
        $definition->setArgument('$messengerQueueActivated', $config['messenger_queue_processing']['activated']);
        if ($config['client_type'] === ClientType::OPEN_SEARCH->value) {
            $openSearchClientId = 'opendxp.open_search_client.' . $config['client_name'];
            $container->setAlias('opendxp.advanced_object_search.opensearch-client', $openSearchClientId)
                ->setDeprecated(
                    'open-dxp/advanced-object-search-bundle',
                    '6.1',
                    'The "%alias_id%" service alias is deprecated and will be removed in version 7.0. ' .
                    'Please use "opendxp.advanced_object_search.search-client" instead.'
                );
        }

        $clientId = $this->getDefaultSearchClientId($config);
        $container->setAlias('opendxp.advanced_object_search.search-client', $clientId);
    }

    public function prepend(ContainerBuilder $container)
    {
        if ($container->hasExtension('doctrine_migrations')) {
            $loader = new YamlFileLoader(
                $container,
                new FileLocator(__DIR__ . '/../Resources/config')
            );

            $loader->load('doctrine_migrations.yml');
        }
    }

    /**
     * @throws RuntimeException
     */
    private function getDefaultSearchClientId(array $indexSettings): string
    {
        $clientType = $indexSettings['client_type'];
        $clientName = $indexSettings['client_name'];

        return match ($clientType) {
            ClientType::OPEN_SEARCH->value => 'opendxp.openSearch.custom_client.' . $clientName,
            ClientType::ELASTIC_SEARCH->value => 'opendxp.elasticsearch.custom_client.' . $clientName,
            default => throw new RuntimeException(
                sprintf('Invalid client type: %s', $clientType)
            )
        };
    }
}
