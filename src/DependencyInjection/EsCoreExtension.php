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
        //$loader->load('doctrine-mapping/User.orm.xml');
        //$loader->load('validator/validation.xml');

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
        $definition->setArgument(4, $config["user_class"]);

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

    public function getAlias()
    {
        return 'es_core';
    }
}
