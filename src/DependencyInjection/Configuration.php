<?php

namespace Es\CoreBundle\DependencyInjection;

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
            //->scalarNode('login_form_authenticator')->defaultNull()->info('Authentication for login form')->end()
            ->end();
        return $treeBuilder;
    }
}
