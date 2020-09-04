<?php

namespace ES\EsCoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EsCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('es_core.es_core');
        if (null !== $config['word_provider']) {
            $container->setAlias('es_core.word_provider', $config['word_provider']);
        }

        if (null !== $config['word_provider']) {
            $definition->setArgument(0, new Reference($config['word_provider']));
        }
        $definition->setArgument(1, $config['min_sunshine']);

        //var_dump($config);die;
    }

    public function getAlias()
    {
        return 'es_core';
    }
}
