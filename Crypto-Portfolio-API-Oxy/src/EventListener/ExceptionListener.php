<?php


namespace App\EventListener;


use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $exceptionResponse = $this->getExceptionResponse($exception);

        $event->setResponse($exceptionResponse);
    }

    private function getExceptionResponse(\Throwable $exception)
    {
        switch ($exception) {
            case($exception instanceof NotFoundHttpException):
                return $this->handleNotFoundHttpException();
            case($exception instanceof ClientException):
                return $this->handleExternalApiException();
        }
    }

    private function handleNotFoundHttpException(): JsonResponse
    {
        $response = [
            'message' => 'Not Found',
            'code' => 404
        ];

        return new JsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    private function handleExternalApiException(): JsonResponse
    {
        $response = [
            'message' => 'External API error',
            'code' => 500
        ];

        return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}