<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 18.07.2018
 * Time: 15:48
 */

namespace AppBundle\Exception;


class InvalidCredentialsException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::INVALID_CREDENTIALS;

        parent::__construct('Неверный пароль', $data);
    }

}