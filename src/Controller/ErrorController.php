<?php

namespace App\Controller;

use App\Response\Violation;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Violation       $violation
    )
    {
    }

    public function show(Throwable $exception): Response
    {
        $this->logger->warning($exception->getMessage());
        $this->logger->warning($exception->getTraceAsString());
        $code = 500;
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        }
        $violation = $this->violation->withMessage($exception->getMessage());
        return $this->acceptWith($violation, $code);
    }
}
