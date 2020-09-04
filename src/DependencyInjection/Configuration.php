<?php

namespace ES\EsCoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('es_core');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            //->booleanNode('unicorns_are_real')->defaultTrue()->end()
            ->integerNode('min_sunshine')->defaultValue(3)->info('How much do you like sunshine?')->end()
            ->scalarNode('word_provider')->defaultNull()->end()
            ->end();
        return $treeBuilder;
    }
}
