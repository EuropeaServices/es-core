<?php
namespace ES\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WordProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('es_core.es_core');
        $references = [];
        foreach ($container->findTaggedServiceIds('es_core_word_provider') as $id => $tags) {
            $references[] = new Reference($id);
        }
        $definition->setArgument(0, $references);
    }
}