<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 18.07.2018
 * Time: 15:17
 */

namespace AppBundle\Exception;


class InvalidDataException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::INVALID_DATA;

        parent::__construct('Неверные данные / не указаны данные', $data);
    }

}