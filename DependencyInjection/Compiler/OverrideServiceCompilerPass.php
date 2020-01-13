<?php
/**
 * Created by PhpStorm.
 * User: evaisse
 * Date: 13/05/15
 * Time: 11:55
 */

namespace evaisse\SimpleAuthBundle\DependencyInjection\Compiler;

use evaisse\SimpleAuthBundle\Security\UserProviderFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $container->getExtension('security')->addUserProviderFactory(new UserProviderFactory());
    }
}
