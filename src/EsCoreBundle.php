<?php

namespace ES\EsCoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use ES\EsCoreBundle\DependencyInjection\EsCoreExtension;

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
}