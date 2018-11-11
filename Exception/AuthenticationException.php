<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 15:28
 */

namespace AppBundle\Exception;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticationException extends HttpException
{

    const INSECURE_CODE_CREATION_ATTEMPT = 1101;

    const USER_EXISTS = 1102;

    const INVALID_USERNAME = 1103;

    const CODE_EXPIRED = 1201;

    const NO_USER_FOR_CHECKING = 1202;

    const BAD_CODE_CHECKING_ATTEMPT = 1203;

    const OUT_OF_CHECKING_ATTEMPTS = 1204;

    const INVALID_DATA = 1301;

    const INVALID_CREDENTIALS = 1302;

    const INVALID_TOKEN = 1401;

    /**
     * @var array
     */
    protected $data;

    /**
     * AuthenticationException constructor.
     * @param null $message
     * @param array $data
     * @param $statusCode
     */
    public function __construct($message = null, Array $data = [], $statusCode = Response::HTTP_OK)
    {
        $this->data = $data;
        $this->data['success'] = false;

        if (null !== $message) {
            $this->data['message'] = $message;
        }

        $this->data['errorCode'] = $this->code;

        parent::__construct($statusCode, $message);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }



}