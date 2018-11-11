<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 18.07.2018
 * Time: 14:35
 */

namespace Tests\AppBundle\Controller;



use AppBundle\Exception\AuthenticationException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{

    public function testLogIn_FailuresWithEmptyData()
    {
        $data = [];

        $this->failuresWithInvalidParams($data);
    }

    public function testLogIn_FailuresWithEmptyUsername()
    {
        $data = [
            'password' => 123456
        ];

        $this->failuresWithInvalidParams($data);
    }

    public function testLogIn_FailuresWithEmptyPassword()
    {
        $data = [
            'username' => 't0wqa'
        ];

        $this->failuresWithInvalidParams($data);
    }

    public function testLogIn_FailuresWithIncorrectPassword()
    {
        // right password: test

        $data = [
            'username' => '89680422975',
            'password' => 'wrong'
        ];

        $client = static::createClient();
        $client->request('POST', 'http://127.0.0.1:8000/login', [], [], [], json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(AuthenticationException::INVALID_CREDENTIALS, json_decode(
            $response->getContent(), true)['errorCode']);
    }

    public function testLogIn_OKWithCorrectCredentials()
    {
        $data = [
            'username' => '89680422975',
            'password' => 'test'
        ];

        $client = static::createClient();
        $client->request('POST', 'http://127.0.0.1:8000/login', [], [], [], json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    private function failuresWithInvalidParams($data)
    {
        $client = static::createClient();
        $client->request('POST', 'http://127.0.0.1:8000/login', [], [], [], json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(AuthenticationException::INVALID_DATA, json_decode(
            $response->getContent(), true)['errorCode']);
    }

}