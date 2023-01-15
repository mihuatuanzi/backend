<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json([
            'name' => [
                'zh' => $this->getParameter('app.name.zh'),
                'en' => $this->getParameter('app.name.en')
            ],
            'app_env' => $this->getParameter('env.app_env'),
            'app_version' => $this->getParameter('env.app_version'),
        ]);
    }
}
