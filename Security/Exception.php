<?php
/**
 * Created by PhpStorm.
 * User: evaisse
 * Date: 12/05/15
 * Time: 09:07
 */

namespace evaisse\SimpleAuthBundle\Security;

/**
 * Class Exception
 * @package evaisse\SimpleAuthBundle\Security
 */
class Exception extends \RuntimeException implements \Serializable
{

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->token,
            $this->code,
            $this->message,
            $this->file,
            $this->line,
        ));
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        list(
            $this->token,
            $this->code,
            $this->message,
            $this->file,
            $this->line
            ) = unserialize($str);
    }
}
