<?php

namespace App\Controller;

use App\Config\AuthCredentialType;
use App\Entity\Authentication;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/user/signup/in-station', name: 'app_user_signup_by_email', methods: ['POST'])]
    public function signupWithInStation(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $authentication = new Authentication();
        $authentication->setCredentialType(AuthCredentialType::tryFrom($request->get('credential_type')));
        $authentication->setCredentialKey($request->get('credential_key'));
        $errors = $validator->validate($authentication, null, ['Authentication', 'InStation']);
        if ($errors->count()) {
            return $this->jsonError($errors);
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
