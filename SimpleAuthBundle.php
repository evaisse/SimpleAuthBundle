<?php

namespace evaisse\SimpleAuthBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use evaisse\SimpleAuthBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;
use evaisse\SimpleAuthBundle\Security\Factory;

class SimpleAuthBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
//
//        $extension = $container->getExtension('security');
//        $extension->addSecurityListenerFactory(new Factory());
//
        $container->addCompilerPass(new OverrideServiceCompilerPass());
    }
}
