<?php

namespace ContainerJwAYHGH;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getApiPlatform_Listener_View_Write_LegacyService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'api_platform.listener.view.write.legacy' shared service.
     *
     * @return \ApiPlatform\Core\EventListener\WriteListener
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/api-platform/core/src/Core/EventListener/WriteListener.php';
        include_once \dirname(__DIR__, 4).'/vendor/api-platform/core/src/Core/DataPersister/DataPersisterInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/api-platform/core/src/Core/DataPersister/ContextAwareDataPersisterInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/api-platform/core/src/Core/Bridge/Symfony/Bundle/DataPersister/TraceableChainDataPersister.php';
        include_once \dirname(__DIR__, 4).'/vendor/api-platform/core/src/Core/DataPersister/ChainDataPersister.php';

        return $container->privates['api_platform.listener.view.write.legacy'] = new \ApiPlatform\Core\EventListener\WriteListener(new \ApiPlatform\Core\Bridge\Symfony\Bundle\DataPersister\TraceableChainDataPersister(new \ApiPlatform\Core\DataPersister\ChainDataPersister(new RewindableGenerator(function () use ($container) {
            yield 0 => ($container->privates['api_platform.doctrine.orm.data_persister'] ?? $container->load('getApiPlatform_Doctrine_Orm_DataPersisterService'));
        }, 1))), ($container->privates['api_platform.iri_converter.legacy'] ?? $container->getApiPlatform_IriConverter_LegacyService()), ($container->privates['api_platform.metadata.resource.metadata_factory.cached'] ?? $container->getApiPlatform_Metadata_Resource_MetadataFactory_CachedService()), ($container->privates['api_platform.resource_class_resolver'] ?? $container->getApiPlatform_ResourceClassResolverService()));
    }
}
