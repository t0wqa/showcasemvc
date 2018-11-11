<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 15:33
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\AuthenticationException;
use AppBundle\Formatter\ErrorFormatter;
use AppBundle\Service\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        if ($exception instanceof HttpException) {
           $response->setContent(
               json_encode(
                   (new ErrorFormatter())->format($exception->getData())
               )
           );

           $response->setStatusCode(Response::HTTP_OK);
           $response->headers->add($exception->getHeaders());

            // sends the modified response object to the event
            $event->setResponse($response);
        }
    }

}