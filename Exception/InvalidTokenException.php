<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 16.07.2018
 * Time: 14:35
 */

namespace AppBundle\Exception;


class InvalidTokenException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::INVALID_TOKEN;

        parent::__construct('Invalid Token', $data);
    }

}