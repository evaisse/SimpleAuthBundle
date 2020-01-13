<?php

namespace evaisse\SimpleAuthBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AuthenticationProvider
 * @package evaisse\SimpleAuthBundle\Security
 */
class AuthenticationProvider implements AuthenticationProviderInterface
{

    protected $userProvider;

    /**
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param TokenInterface $token
     * @return UserToken
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->refreshUser($token->getUser());

        if ($user) {
            $authenticatedToken = new UserToken($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The authentication failed.');
    }

    /**
     * @param TokenInterface $token
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof UserToken;
    }
}
