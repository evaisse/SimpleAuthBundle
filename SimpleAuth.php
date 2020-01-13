<?php
/**
 * Created by PhpStorm.
 * User: evaisse
 * Date: 11/05/15
 * Time: 18:46
 */

namespace evaisse\SimpleAuthBundle;

use evaisse\SimpleAuthBundle\Event\UserLoginEvent;
use evaisse\SimpleAuthBundle\Event\UserLogoutEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use evaisse\SimpleAuthBundle\Security\UserToken;
use evaisse\SimpleAuthBundle\Security\User;

/**
 * Class SimpleAuthFacade
 * @package evaisse\SimpleAuthBundle\Service
 */
class SimpleAuth implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * SimpleAuthFacade constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenStorageInterface    $tokenStorage
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     * @param User    $user
     * @param string  $firewall
     * @param array   $roles
     */
    public function loginWithRequest(Request $request, User $user, $firewall = "simple_auth_firewall")
    {
        $this->logoutWithRequest($request);

        if (!$request->getSession()) {
            throw new SimpleAuthException('session must be available');
        }

        $token = new UserToken($user);
        $this->tokenStorage->setToken($token);
        $event = new UserLoginEvent($request, $token);
        $this->eventDispatcher->dispatch("security.interactive_login", $event);
        $request->getSession()->set('_security_'.$firewall, serialize($token));
    }

    /**
     * @param Request $request  Input request
     * @param string  $firewall firewall name against which the user should be logged out
     * @return bool true if user logout, false otherwise
     */
    public function logoutWithRequest(Request $request, $firewall = "simple_auth_firewall")
    {
        $oldToken = $this->tokenStorage->getToken();
        $this->tokenStorage->setToken(null);

        if ($request->getSession()->has('_security_'.$firewall)) {
            $this->logger->info('logout user', ['trace' => debug_backtrace()]);
            $event = new UserLogoutEvent($request, $oldToken);
            $this->eventDispatcher->dispatch("security.interactive_logout", $event);
            $request->getSession()->remove('_security_'.$firewall);
            return true;
        }

        $this->logger->info('do not logout user', ['trace' => debug_backtrace()]);

        return false;
    }

    /**
     * @param Request $request
     * @param string  $firewall
     * @return mixed|null
     */
    public function getToken(Request $request, $firewall = "simple_auth_firewall")
    {
        if ($request->getSession()->has('_security_'.$firewall)) {
            return unserialize($request->getSession()->get('_security_'.$firewall), ['allowed_classes' => true]);
        } else {
            return null;
        }
    }
    /**
     * @return User
     */
    public function getUser(): User
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (is_object($user) && !$user instanceof User) {
            return null;
        }

        return $user;
    }
}
