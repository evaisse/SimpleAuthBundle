<?php
/**
 * User: evaisse
 * Date: 13/01/2020
 * Time: 19:21
 */

namespace evaisse\SimpleAuthBundle\Event;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class LoginEvent
 * @package evaisse\SimpleAuthBundle\Event
 */
class UserLoginEvent extends InteractiveLoginEvent
{

}