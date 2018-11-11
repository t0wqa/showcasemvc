<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 8:35
 */

namespace AppBundle\Exception;


class InvalidUsernameException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::INVALID_USERNAME;

        parent::__construct('Неверно введен телефон/email', $data);
    }

}