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
                ->arrayNode('resetting')
                ->addDefaultsIfNotSet()
                ->canBeUnset()
                ->children()
                    ->scalarNode('retry_ttl')->defaultValue(7200)->info('Ttl for resetting password by resetting')->end()
            //->scalarNode('login_form_authenticator')->defaultNull()->info('Authentication for login form')->end()
                ->end()
            ->end()

            ->arrayNode('mailler')
                ->addDefaultsIfNotSet()
                ->canBeUnset()
                ->children()
                    ->scalarNode('mail_to_dev')->defaultValue("support_applicatif@europeaservices.com")
                        ->info('Mail adress those mails sended in dev environnement')->end()
                    ->scalarNode('mail_from')->defaultValue("support_applicatif@europeaservices.com")
                        ->info('Mail from address')->end()
            //->scalarNode('login_form_authenticator')->defaultNull()->info('Authentication for login form')->end()
                ->end()
            ->end()

            ->end();
        return $treeBuilder;
    }
}
