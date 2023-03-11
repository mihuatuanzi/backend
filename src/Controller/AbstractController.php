<?php

namespace App\Controller;

use App\Interface\StructureResponse;
use App\Response\AcceptData;
use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractController extends Controller\AbstractController
{
    public function acceptWith(
        StructureResponse $structureResponse,
        int $status = 200,
        array $headers = [],
        array $context = []
    ): JsonResponse
    {
        $acceptData = new AcceptData();
        $acceptData->attach($structureResponse);
        return $this->json([
            AcceptData::ID => $acceptData,
            'version' => $this->getParameter('env.app_version')
        ], $status, $headers, $context);
    }

    /**
     * 根据 ConstraintViolationList 返回 json 格式的错误信息
     *
     * @param ConstraintViolationListInterface $violationMap
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    public function jsonErrorsForConstraints(ConstraintViolationListInterface $violationMap, int $status = 417, array $headers = [], array $context = []): JsonResponse
    {
        $errorMap = ['_violations' => []];
        foreach ($violationMap as $violation) {
            if ($propertyPath = $violation->getPropertyPath()) {
                $errorMap[$propertyPath] = $violation->getMessage();
            } else {
                $errorMap['_violations'][] = $violation->getMessage();
            }
        }
        return $this->jsonErrors(['violations' => $errorMap], $status, $headers, $context);
    }

    /**
     * 返回 json 格式的错误信息
     *
     * @param array $errorMap
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    public function jsonErrors(array $errorMap, int $status = 417, array $headers = [], array $context = []): JsonResponse
    {
        return $this->json($errorMap, $status, $headers, $context);
    }
}
