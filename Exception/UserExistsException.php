<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 9:17
 */

namespace AppBundle\Exception;


class UserExistsException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::USER_EXISTS;

        parent::__construct('Пользователь с указанными данными уже существует', $data);
    }

}