<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 10:57
 */

namespace AppBundle\Exception;


class CodeExpiredException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::CODE_EXPIRED;

        parent::__construct('Запрошенный код больше не валиден. Нужно запросить новый', $data);
    }

}