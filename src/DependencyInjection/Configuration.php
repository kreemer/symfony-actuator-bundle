<?php

declare(strict_types=1);

namespace Akondas\ActuatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('actuator');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('health')
                    ->canBeDisabled()
                    ->children()
                        ->arrayNode('builtin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('disk_space')
                                    ->addDefaultsIfNotSet()
                                    ->canBeDisabled()
                                    ->children()
                                        ->integerNode('threshold')->defaultValue(50 * 1024 * 1024)->end()
                                        ->scalarNode('path')->defaultValue('%kernel.project_dir%')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('database')
                                    ->addDefaultsIfNotSet()
                                    ->canBeDisabled()
                                    ->children()
                                        ->arrayNode('connections')
                                            ->useAttributeAsKey('name')
                                            ->defaultValue(['default' => ['service' => 'doctrine.dbal.default_connection', 'check_sql' => 'SELECT 1']])
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('service')->isRequired()->end()
                                                    ->scalarNode('check_sql')->defaultValue('SELECT 1')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('mailer')
                                    ->addDefaultsIfNotSet()
                                    ->canBeDisabled()
                                    ->children()
                                        ->arrayNode('transports')
                                            ->useAttributeAsKey('name')
                                            ->defaultValue(['default' => ['service' => 'mailer.default_transport']])
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('service')->isRequired()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('info')
                    ->addDefaultsIfNotSet()
                    ->canBeDisabled()
                    ->children()
                        ->arrayNode('builtin')
                            ->children()
                                ->arrayNode('php')
                                    ->canBeDisabled()
                                ->end()
                                ->arrayNode('symfony')
                                    ->canBeDisabled()
                                ->end()
                                ->arrayNode('git')
                                    ->canBeDisabled()
                                ->end()
                                ->arrayNode('mailer')
                                    ->canBeDisabled()
                                    ->children()
                                        ->arrayNode('transports')
                                            ->useAttributeAsKey('name')
                                            ->defaultValue(['default' => 'mailer.default_transport'])
                                            ->scalarPrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('database')
                                    ->canBeDisabled()
                                    ->children()
                                        ->arrayNode('connections')
                                            ->useAttributeAsKey('name')
                                            ->defaultValue(['default' => 'doctrine.dbal.default_connection'])
                                            ->scalarPrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
