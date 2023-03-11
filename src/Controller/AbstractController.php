<?php

namespace App\Controller;

use App\Interface\StructureResponse;
use App\Response\AcceptData;
use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController extends Controller\AbstractController
{
    public function acceptWith(
        StructureResponse|array $structureResponse,
        int $status = 200,
        array $headers = [],
        array $context = []
    ): JsonResponse
    {
        $acceptData = new AcceptData();
        $acceptData->attach($structureResponse);
        return $this->json([
            AcceptData::SINGULAR => $acceptData,
            'version' => $this->getParameter('env.app_version')
        ], $status, $headers, $context);
    }
}
