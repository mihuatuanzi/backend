<?php

namespace App\Controller;

use App\Config\AuthCredentialType;
use App\Entity\Authentication;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\Edge;
use App\Validator\SuppressDuplicateCredential;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 用户身份相关
 *
 * @controller
 */
class AuthController extends AbstractController
{
    #[Route('/auth/send-email-verification', name: 'auth_send_email_verification', methods: ['POST'])]
    public function sendEmailVerification(
        Request            $request,
        ValidatorInterface $validator,
        Edge               $edge,
    ): Response
    {
        $credentialKey = $request->get('credential_key');
        $errors = $validator->validate($credentialKey, [
            new Assert\Email(null, '值不是有效的电子邮件地址'),
            new SuppressDuplicateCredential(AuthCredentialType::Email)
        ]);
        if ($errors->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        if ($edge->sendVerificationMail($credentialKey)) {
            return $this->json(['message' => '邮件发送成功', 'verify_token' => 'xxx'], 201);
        }
        return $this->jsonErrors(['message' => '邮件发送失败'], 500);
    }

    #[Route('/auth/verify-email', name: 'auth_verify_email', methods: ['GET'])]
    public function verifyEmail(
        Request                  $request,
        ValidatorInterface       $validator,
        UserRepository           $userRepository,
        AuthenticationRepository $authenticationRepository,
        Edge                     $edge,
    ): Response
    {
        $password = $request->get('password');
        $payload = $edge->decodeJwtToken($request->get('verify_token'));
        if (!$payload || !$password) {
            return $this->jsonErrors(['message' => '无法激活账户']);
        }
        $credentialKey = $payload['email'];
        $auth = new Authentication();
        $auth->setCredentialType(AuthCredentialType::Email)
            ->setCredentialKey($credentialKey)
            ->setCreatedAt(new DateTimeImmutable());
        $errors = $validator->validate($auth);
        if ($errors->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $userRepository->save($auth->initializeUser(), true);
        $authenticationRepository->save($auth, true);

        return $this->json(['user' => $auth->getUser()]);
    }
}
