<?php

namespace Devoralive\TrafficLimit\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('traffic_limit');

        $rootNode
            ->children()
                ->scalarNode('enabled')
                    ->defaultValue('true')
                    ->setInfo('Enable or disable the traffic limit service')
                    ->setExample('true|false')
                ->end()
                ->scalarNode('redis_dsn')
                    ->defaultValue('redis://localhost')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
