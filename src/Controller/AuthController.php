<?php

namespace App\Controller;

use App\Config\AuthCredentialType;
use App\Entity\Authentication;
use App\Entity\User;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\Authentic;
use App\Validator\SuppressDuplicateCredential;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    #[Route('/auth/send-email-verification', name: 'auth_send_email_verification', methods: ['POST'])]
    public function sendEmailVerification(
        Request            $request,
        Authentic          $authentic,
        ValidatorInterface $validator,
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
    #[IsGranted('ROLE_USER')]
    #[Route('/auth/verify-email', name: 'auth_verify_email', methods: ['POST'])]
    public function verifyEmail(
        Request                     $request,
        Authentic                   $authentic,
        UserRepository              $userRepository,
        ValidatorInterface          $validator,
        AuthenticationRepository    $authenticationRepository,
        UserPasswordHasherInterface $passwordHashTool,
    ): JsonResponse
    {
        $password = $request->get('password');
        if ($errors = $authentic->validatePassword($password)) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $email = $authentic->getEmailByVerifyToken(
            $request->get('verify_token'),
            $request->attributes->get('_route')
        );
        if (!$email) {
            return $this->jsonErrors(['message' => '无法激活账户']);
        }

        $auth = new Authentication();
        $auth->setCredentialType(AuthCredentialType::Email)
            ->setCredentialKey($email)
            ->setCreatedAt(new DateTimeImmutable());
        if (($errors = $validator->validate($auth))->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $user = $auth->initializeUser();
        $hashedPassword = $passwordHashTool->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $userRepository->save($auth->getUser(), true);
        $authenticationRepository->save($auth, true);

        return $this->acceptUserAuth($user);
    }

    /**
     * 重置密码 Email 方式
     */
    #[Route('/auth/reset-password-by-email', name: 'auth_reset_password_by_email', methods: ['POST'])]
    public function resetPasswordByEmail(
        Request                     $request,
        Authentic                   $authentic,
        UserRepository              $userRepository,
        AuthenticationRepository    $authenticationRepository,
        UserPasswordHasherInterface $passwordHashTool,
    ): JsonResponse
    {
        $password = $request->get('password');
        if ($errors = $authentic->validatePassword($password)) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $email = $authentic->getEmailByVerifyToken(
            $request->get('verify_token'),
            $request->attributes->get('_route')
        );
        if (!$email) {
            return $this->jsonErrors(['message' => '无法激活账户']);
        }

        $auth = $authenticationRepository->findOneBy([
            'credential_type' => AuthCredentialType::Email,
            'credential_key' => $email
        ]);
        if (null === $auth) {
            return $this->jsonErrors(['message' => '邮箱未注册']);
        }
        $user = $auth->getUser();

        $hashedPassword = $passwordHashTool->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $userRepository->save($user, true);

        return $this->json(['message' => '更新成功']);
    }

    /**
     * 返回登录用户信息
     *
     * @param User $user
     * @return JsonResponse
     */
    private function acceptUserAuth(User $user): JsonResponse
    {
        return $this->json([
            'user' => [
                'internalId' => $user->getId(),
                'uniqueId' => $user->getUserIdentifier(),
                'nickname' => $user->getNickname(),
                'gender' => $user->getGender(),
                'avatar' => $user->getAvatar(),
                'signature' => $user->getSignature(),
                'createdAt' => $user->getCreatedAt()
            ]
        ]);
    }
}
