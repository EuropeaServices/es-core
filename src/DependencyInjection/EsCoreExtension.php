<?php

namespace Es\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Es\CoreBundle\Repository\Security\UserRepository;

class EsCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('repositories.xml');
        $loader->load('controllers.xml');
        //$loader->load('validator/validation.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('es_core.security.utils');
        $definition->setArgument(1, $config["resetting"]['retry_ttl']);

        $definition = $container->getDefinition('es_core.email');
        $definition->setArgument(1, $config["mailler"]['mail_to_dev']);
        $definition->setArgument(2, $config["mailler"]['mail_from']);

        /*$container->registerForAutoconfiguration(UserRepository::class)
            ->addTag('doctrine.repository_service');*/

        /*$definition = $container->getDefinition('es_core.controller.security_controller');
        if (null !== $config['login_form_authenticator']) {
            $definition->setArgument(0, $config['login_form_authenticator']);
        }*/
    }

    public function getAlias()
    {
        return 'es_core';
    }
}
