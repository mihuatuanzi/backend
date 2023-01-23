<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    public function show(Throwable $exception): Response
    {
        $this->logger->warning($exception->getMessage());
        $this->logger->warning($exception->getTraceAsString());
        if ($exception instanceof HttpException) {
            return $this->jsonErrors([
                'message' => $exception->getMessage()
            ], $exception->getStatusCode());
        }
        return $this->jsonErrors(['message' => $exception->getMessage()], 500);
    }
}
