<?php

namespace Devoralive\TrafficLimit\DependencyInjection;

use Devoralive\TrafficLimit\Services\TrafficLimitService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * TrafficLimitExtension
 *
 * Register services, check dependencies.
 */
class TrafficLimitExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Check SncRedisBundle is enabled:
        if (!array_key_exists('SncRedisBundle', $container->getParameter('kernel.bundles'))) {
            throw new \RuntimeException('The "SncRedisBundle" must be configured');
        }

        // Merge configs of environments
        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        // Create the traffic_limit.services:
        foreach ($config as $key => $clientConfig) {
            $container->register('traffic_limit.' . $key, TrafficLimitService::class)
                ->addArgument($clientConfig['enabled'])
                ->addArgument(
                    new Reference(
                        'snc_redis.' . $clientConfig['snc_client']
                    )
                )
                ->addArgument($clientConfig['amount'])
                ->addArgument($clientConfig['ttl'])
                ->addArgument($key);
        }
    }
}
