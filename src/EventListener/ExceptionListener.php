<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        // Customize your response object to display the exception details
        $response = new JsonResponse();
        $response->setData([
            'status' => $exception->getCode(),
            'message' => $message,
        ]);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        try {
            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
            }
        } catch (\Exception $e) {
            $response->setStatusCode(500);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
