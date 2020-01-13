<?php
/**
 * User: evaisse
 * Date: 13/01/2020
 * Time: 19:22
 */

namespace evaisse\SimpleAuthBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserRefreshEvent
 * @package evaisse\SimpleAuthBundle\Event
 */
class UserRefreshEvent extends Event
{

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var TokenInterface
     */
    protected $oldAuthenticationToken;
    /**
     * @var TokenInterface
     */
    protected $newAuthenticationToken;

    /**
     * UserRefreshEvent constructor.
     * @param Request        $request
     * @param TokenInterface $oldAuthenticationToken
     * @param TokenInterface $newAuthenticationToken
     */
    public function __construct(Request $request, TokenInterface $oldAuthenticationToken, TokenInterface $newAuthenticationToken)
    {
        $this->request = $request;
        $this->oldAuthenticationToken = $oldAuthenticationToken;
        $this->newAuthenticationToken = $newAuthenticationToken;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return TokenInterface
     */
    public function getOldAuthenticationToken(): TokenInterface
    {
        return $this->oldAuthenticationToken;
    }

    /**
     * @return TokenInterface
     */
    public function getNewAuthenticationToken(): TokenInterface
    {
        return $this->newAuthenticationToken;
    }



}