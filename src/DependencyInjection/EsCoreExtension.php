<?php

namespace Es\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EsCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
        $loader->load('repositories.xml');
        $loader->load('controllers.xml');
        $loader->load('events.xml');
        $loader->load('commands.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('es_core.security.utils');
        $definition->setArgument(1, $config["resetting"]['retry_ttl']);

        $definition = $container->getDefinition('es_core.email');
        $definition->setArgument(1, $config["mailler"]['mail_to_dev']);
        $definition->setArgument(2, $config["mailler"]['mail_from']);

        $definition = $container->getDefinition('es_core.login_form_authenticator');
        $definition->setArgument(4, $config["user_class"]);

        $definition = $container->getDefinition('es_core.controller.security.resetting_controller');
        $definition->setArgument(5, $config["user_class"]);
        $definition->setArgument(6, $config["password_validation"]["minimal_length"]);
        $definition->setArgument(7, $config["password_validation"]["regex_validation"]);

        $definition = $container->getDefinition('es_core.controller.security.change_password_controller');
        $definition->setArgument(4, $config["password_validation"]["minimal_length"]);
        $definition->setArgument(5, $config["password_validation"]["regex_validation"]);

        $definition = $container->getDefinition('es_core.event_listener.doctrine.load_class_metadata');
        $definition->setArgument(0, $config["user_class"]);

        $definition = $container->getDefinition('es_core.event_listener.security.change_password');
        $definition->setArgument(2, $config["password_expiration"]["number"]);
        $definition->setArgument(3, $config["password_expiration"]["unity"]);
        $definition->setArgument(4, $config["password_expiration"]["route_name_change_password"]);

        $definition = $container->getDefinition('es_core.mailer.utils');
        $definition->setArgument(2, $config["user_class"]);
        $definition->setArgument(3, $config["password_expiration"]["number"]);
        $definition->setArgument(4, $config["password_expiration"]["unity"]);
        $definition->setArgument(5, $config["send_mail_warning_password_expired"]["number"]);
        $definition->setArgument(6, $config["send_mail_warning_password_expired"]["unity"]);
        

        /*$container->registerForAutoconfiguration(UserRepository::class)
            ->addTag('doctrine.repository_service');*/

        /*$definition = $container->getDefinition('es_core.controller.security_controller');
        if (null !== $config['login_form_authenticator']) {
            $definition->setArgument(0, $config['login_form_authenticator']);
        }*/

        //Doctrine
        /*$arrayDoctrineToLoad = array(
            __DIR__ . '/../Resources/config/doctrine-mapping/User.orm.xml'
        );
        $config = new \Doctrine\ORM\Configuration();
        $driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver($arrayDoctrineToLoad);
        $config->setMetadataDriverImpl($driver);*/
    }

    public function getAlias():string
    {
        return 'es_core';
    }
}
