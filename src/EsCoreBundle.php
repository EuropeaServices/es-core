<?php

namespace Es\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Es\CoreBundle\DependencyInjection\EsCoreExtension;
use Es\CoreBundle\DependencyInjection\Compiler\WordProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class EsCoreBundle extends Bundle
{

        /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension():?Extension
    {
        if (null === $this->extension) {
            $this->extension = new EsCoreExtension();
        }
        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
    }
}