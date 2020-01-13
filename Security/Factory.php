<?php


namespace evaisse\SimpleAuthBundle\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * Class Factory
 * @package evaisse\SimpleAuthBundle\Securitymixed
 */
class Factory implements SecurityFactoryInterface
{

    /**
     * @param ContainerBuilder $container
     * @param mixed            $id
     * @param mixed            $config
     * @param mixed            $userProviderId
     * @param mixed            $defaultEntryPoint
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPoint)
    {
        $providerId  = 'simple_auth.security.authentication.provider';

        $container
            ->setDefinition($providerId, new DefinitionDecorator('simple_auth.security.authentication.provider'))
            ->replaceArgument(0, new Reference('simple_auth.security.user_provider'))
        ;

        $listenerId = 'simple_auth.security.authentication.listener';

        $container->setDefinition($listenerId, new DefinitionDecorator('simple_auth.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'simple_auth';
    }

    /**
     * @param NodeDefinition $builder
     */
    public function addConfiguration(NodeDefinition $builder)
    {

    }
}
