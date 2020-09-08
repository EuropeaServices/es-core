<?php

namespace ES\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use ES\CoreBundle\DependencyInjection\EsCoreExtension;
use ES\CoreBundle\DependencyInjection\Compiler\WordProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EsCoreBundle extends Bundle
{

        /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EsCoreExtension();
        }
        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(WordProviderInterface::class)
            ->addTag('es_core_word_provider');
        $container->addCompilerPass(new WordProviderCompilerPass());
    }
}