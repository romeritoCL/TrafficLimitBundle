<?php

namespace Devoralive\TrafficLimit\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TrafficLimitExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if (!isset($config['redis_dsn'])) {
            throw new \InvalidArgumentException('The "redis_dsn" option must be set');
        }

        $container->setParameter('traffic_limit.enabled', $config['enabled']);
        $container->setParameter('traffic_limit.redis_dsn', $config['redis_dsn']);

        $loader->load('services.yml');
    }
}
