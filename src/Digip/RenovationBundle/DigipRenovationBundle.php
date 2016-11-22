<?php

namespace Digip\RenovationBundle;

use Digip\RenovationBundle\DependencyInjection\Compiler\EnvironmentCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DigipRenovationBundle extends Bundle
{
    /**
     * Set FOSUserBundle as parent so we can override its templates
     *
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }

    public function build(ContainerBuilder $container)
    {
    }
}
