<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 10:26
 */

namespace AppBundle\Exception;


class InsecureCodeCreationAttemptException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::INSECURE_CODE_CREATION_ATTEMPT;

        parent::__construct('Прошло недостаточно времени для запроса нового кода', $data);
    }

}