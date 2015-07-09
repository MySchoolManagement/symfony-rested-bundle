<?php
namespace Rested\Bundle\RestedBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rested');

        $rootNode
            ->children()
                ->scalarNode('prefix')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('resources')
                    ->prototype('scalar')->end()
                ->end() // resources
            ->end()
        ;

        return $treeBuilder;
    }
}