<?php
/**
 * User: evaisse
 * Date: 13/01/2020
 * Time: 19:21
 */

namespace evaisse\SimpleAuthBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LogoutEvent
 * @package evaisse\SimpleAuthBundle\Event
 */
class UserLogoutEvent extends Event
{

    public function __construct(Request $request, TokenInterface $authenticationToken)
    {
        $this->request = $request;
        $this->authenticationToken = $authenticationToken;
    }

}