<?php

namespace App\Controller;

use App\Config\AuthCredentialType;
use App\Config\SendVerificationScene;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Response\Certificate;
use App\Service\Authentic;
use App\Validator\SuppressDuplicateCredential;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 用户身份相关
 */
class AuthController extends AbstractController
{
    /**
     * 发送 email 验证链接
     */
    #[Route('/auth/send-verification-email', name: 'auth_send_verification_email', methods: ['POST'])]
    public function sendVerificationEmail(
        Request            $request,
        Authentic          $authentic,
        ValidatorInterface $validator,
    ): Response
    {
        $scene = $request->get('scene');
        $credentialKey = $request->get('email');

        $constraints = [new Assert\Email(null, '值不是有效的电子邮件地址')];
        if (SendVerificationScene::SignUp === SendVerificationScene::from($scene)) {
            $constraints[] = new SuppressDuplicateCredential(AuthCredentialType::Email);
        }
        $errors = $validator->validate($credentialKey, $constraints);
        if ($errors->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $verifyToken = $authentic->makeVerifyToken($credentialKey);
        if ($authentic->sendVerificationMail($credentialKey)) {
            return $this->json(['message' => '邮件发送成功', 'verify_token' => $verifyToken], 201);
        }
        return $this->jsonErrors(['message' => '邮件发送失败'], 500);
    }

    /**
     * Email 注册流程
     * 验证邮箱并创建账号
     */
    #[Route('/auth/verify-email-and-set-password', name: 'auth_verify_email_and_set_pwd', methods: ['POST'])]
    public function verifyEmailAndSetPassword(
        Request                     $request,
        Authentic                   $authentic,
        UserRepository              $userRepository,
        AuthenticationRepository    $authenticationRepository,
        UserPasswordHasherInterface $passwordHashTool,
    ): JsonResponse
    {
        $password = $request->get('password');
        $verifyToken = $request->get('verify_token');
        $routeName = $request->attributes->get('_route');

        if ($password && $errors = $authentic->validatePassword($password)) {
            return $this->jsonErrorsForConstraints($errors);
        }

        if (!($email = $authentic->getEmailByVerifyToken($verifyToken, $routeName))) {
            return $this->jsonErrors(['message' => '无法激活账户']);
        }

        $auth = $authenticationRepository->findOrCreateByEmail($email);
        if ($password) {
            $user = $auth->getUser();
            $user->setPassword($passwordHashTool->hashPassword($user, $password));
            $userRepository->save($user, true);
        }

        $authenticationRepository->save($auth, true);

        return $this->json(['message' => 'Succeed']);
    }

    #[Route('/auth/sign-in-by-email', name: 'sign_in_by_email', methods: ['POST'])]
    public function signInByEmail(
        Request                     $request,
        Authentic                   $authentic,
        Certificate                 $certificate,
        ValidatorInterface          $validator,
        AuthenticationRepository    $authenticationRepository,
        UserPasswordHasherInterface $passwordHashTool,
    ): JsonResponse
    {
        $credentialKey = $request->get('email');
        $password = $request->get('password');

        $errors = $validator->validate($credentialKey, new Assert\Email(null, '值不是有效的电子邮件地址'));
        if ($errors->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        if ($errors = $authentic->validatePassword($password)) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $auth = $authenticationRepository->findOneBy([
            'credential_type' => AuthCredentialType::Email, 'credential_key' => $credentialKey
        ]);
        if (!$auth) {
            return $this->jsonErrors(['message' => '邮箱尚未注册']);
        }

        $user = $auth->getUser();
        if ($passwordHashTool->isPasswordValid($user, $password)) {
            return $this->json(['certificate' => $certificate->withUser($user)]);
        }

        return $this->jsonErrors(['message' => '账号或密码不正确']);
    }
}
