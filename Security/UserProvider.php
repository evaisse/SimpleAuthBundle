<?php
/**
 * @author Emmanuel VAISSE
 *
 * changelog:
 *     Emmanuel VAISSE - 2015-09-22 18:50:58
 */
namespace evaisse\SimpleAuthBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class UserProvider
 * @package evaisse\SimpleAuthBundle\Security
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @param string $username
     * @return User
     */
    public function loadUserByUsername($username)
    {
        /*
         * Disable in this case
         */
        return false;
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'evaisse\SimpleAuthBundle\Security\User\User';
    }
}
