<?php
/**
 * Created by PhpStorm.
 * User: evaisse
 * Date: 13/05/15
 * Time: 12:11
 */

namespace evaisse\SimpleAuthBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UserProviderFactory implements UserProviderFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config)
    {
        $definition = $container->setDefinition($id, new DefinitionDecorator('simple_auth.security.user_provider'));
    }

    public function getKey()
    {
        return "simple_auth.security.user.provider.factory";
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        // ...
    }
}
