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
        $body = [
            AcceptData::KEY_SINGULAR => (new AcceptData())->attach($structureResponse),
            'version' => $this->getParameter('env.app_version')
        ];
        return $this->json($body, $status, $headers, $context);
    }
}
