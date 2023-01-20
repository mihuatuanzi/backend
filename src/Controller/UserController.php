<?php

namespace App\Controller;

use App\Config\AuthCredentialType;
use App\Entity\Authentication;
use App\Entity\User;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/user/signup/in-station', name: 'app_user_signup_by_email', methods: ['POST'])]
    public function signupWithInStation(
        Request $request,
        ValidatorInterface $validator,
        UserRepository $userRepository,
        AuthenticationRepository $authenticationRepository
    ): JsonResponse
    {
        $now = new DateTimeImmutable();
        $credentialType = AuthCredentialType::tryFrom($request->get('credential_type'));
        $credentialKey = $request->get('credential_key');
        $authentication = new Authentication();
        $authentication->setCredentialType($credentialType);
        $authentication->setCredentialKey($credentialKey);
        $authentication->setCreatedAt($now);
        $errors = $validator->validate($authentication, null, ['Authentication', 'InStation']);
        if ($errors->count()) {
            return $this->jsonError($errors);
        }

        $user = new User();
        $user->setUniqueId(Uuid::uuid7());
        $user->setNickname($credentialKey);
        $user->setCreatedAt($now);
        $authentication->setUser($user);

        $userRepository->save($user, true);
        $authenticationRepository->save($authentication, true);

        return $this->json([
            'data' => [
                'id' => $user->getId(),
                'uniqueId' => $user->getUniqueId()
            ],
        ]);
    }
}
