<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 11:21
 */

namespace AppBundle\Exception;


class NoUserForCheckingException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::NO_USER_FOR_CHECKING;

        parent::__construct('Нет данных для проверки', $data);
    }

}