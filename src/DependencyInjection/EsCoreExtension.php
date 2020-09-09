<?php

namespace Es\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Es\CoreBunble\WordProviderInterface;

class EsCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

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
