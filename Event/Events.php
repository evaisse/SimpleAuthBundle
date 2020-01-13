<?php
/**
 * User: evaisse
 * Date: 13/01/2020
 * Time: 19:21
 */

namespace evaisse\SimpleAuthBundle\Event;

/**
 * Class Events
 * @package evaisse\SimpleAuthBundle\Event
 */
abstract class Events
{

    public const ON_USER_REFRESH = 'simple_auth.user_refresh';
    public const ON_USER_LOGIN = 'simple_auth.user_login';
    public const ON_USER_LOGOUT = 'simple_auth.user_logout';

}