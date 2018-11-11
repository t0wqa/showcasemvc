<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 10:52
 */

namespace AppBundle\Exception;


class BadCodeCheckingAttemptException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::BAD_CODE_CHECKING_ATTEMPT;

        parent::__construct('Неверный код', $data);
    }

}