<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 11:01
 */

namespace AppBundle\Exception;


class OutOfCheckingAttemptsException extends AuthenticationException
{

    public function __construct(array $data = [])
    {
        $this->code = static::OUT_OF_CHECKING_ATTEMPTS;

        parent::__construct('Не осталось попыток по данному коду. Необходимо запросить новый', $data);
    }

}