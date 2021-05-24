<?php

namespace Es\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration of the config file of es core
 * 
 * @author hroux
 * @since 11/11/2020
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Configuration of the config file
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('es_core');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()

                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('retry_ttl')
                            ->defaultValue(7200)
                            ->info('Ttl for resetting password by resetting')
                        ->end()
            //->scalarNode('login_form_authenticator')->defaultNull()->info('Authentication for login form')->end()
                    ->end()
                ->end()

                ->arrayNode('mailler')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('mail_to_dev')
                            ->defaultValue("hroux@europeaservices.com")
                            ->info('Mail adress those mails sended in dev environnement')
                        ->end()
                        ->scalarNode('mail_from')
                            ->defaultValue("support_applicatif@europeaservices.com")
                            ->info('Mail from address')
                        ->end()
            //->scalarNode('login_form_authenticator')->defaultNull()->info('Authentication for login form')->end()
                    ->end()
                ->end()

                ->scalarNode('user_class')
                    ->defaultValue("Es\CoreBundle\Entity\Security\User")
                    ->info('User Class using')
                ->end()

                ->arrayNode('password_validation')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('minimal_length')
                            ->defaultValue(11)
                            ->info("Minimal password length")
                        ->end()
                        ->scalarNode('regex_validation')
                            ->defaultValue('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!*?&#-()_<>\.\/:§;,$£|€+=°])[A-Za-z\d@$!*?&#-()_<>\.\/:§;,$£|€+=°]+$/')
                            ->info("Regax password validation")
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('password_expiration')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('number')
                            ->defaultValue(6)
                            ->info("Number of [unity] (day, month, year, ...) that the password is considered expired")
                        ->end()
                        ->scalarNode('unity')
                            ->defaultValue('Month')
                            ->info("Referenciel of expiration")
                        ->end()
                        ->scalarNode('route_name_change_password')
                            ->defaultValue('es_core_change_password')
                            ->info("Name of route change password")
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('send_mail_warning_password_expired')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('number')
                            ->defaultValue(10)
                            ->info("Number of [unity] (day, month, year, ...) that the password is considered expired")
                        ->end()
                        ->scalarNode('unity')
                            ->defaultValue('Day')
                            ->info("Referenciel of expiration")
                        ->end()
                    ->end()
                ->end()

            ->end();
        return $treeBuilder;
    }
}
