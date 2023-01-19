<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractController extends Controller\AbstractController
{
    public function jsonError(ConstraintViolationListInterface $errors, int $status = 417, array $headers = [], array $context = []): JsonResponse
    {
        $errorMap = [];
        foreach ($errors as $error) {
            $errorMap[$error->getPropertyPath()] = $error->getMessage();
        }
        return $this->json(['errors' => $errorMap], $status, $headers, $context);
    }
}
