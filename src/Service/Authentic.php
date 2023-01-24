<?php

namespace App\Service;

use App\Entity\User;
use App\Interface\EmailDelivery;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class Authentic
{
    private const ACCESS_TOKEN_EXPIRED = 3600 * 2;

    public function __construct(
        private ValidatorInterface    $validator,
        private EmailDelivery         $email,
        private UrlGeneratorInterface $router,
        private ParameterBagInterface $parameterBag,
    )
    {
    }

    public function validatePassword(string $rawPassword): ?ConstraintViolationListInterface
    {
        $errors = $this->validator->validate($rawPassword, [
            new Assert\Length(min: 6, max: 32),
            new Assert\Regex(pattern: '/[\w!@#$%^&*-+]/')
        ]);
        if ($errors->count()) {
            return $errors;
        }
        return null;
    }

    public function makeAccessToken(User $user): array
    {
        $now = time();
        $expiredTime = $now + self::ACCESS_TOKEN_EXPIRED;
        $privateKey = file_get_contents($this->parameterBag->get('app.secret.private'));
        $payload = [
            'iss' => 'mihuatuanzi.com',
            'aud' => 'mihuatuanzi.com',
            'iat' => $now,
            'exp' => $expiredTime,
            'sub' => $user->getUserIdentifier(),
        ];
        $accessToken = JWT::encode($payload, $privateKey, 'RS256');
        $payload['nbf'] = $expiredTime - floor(self::ACCESS_TOKEN_EXPIRED / 2);
        $refreshToken = JWT::encode($payload, $privateKey, 'RS256');
        return [$accessToken, $refreshToken, $expiredTime, $payload['nbf']];
    }

    public function makeVerifyToken(string $email): string
    {
        $privateKey = file_get_contents($this->parameterBag->get('app.secret.private'));
        $payload = [
            'iss' => 'mihuatuanzi.com',
            'aud' => 'mihuatuanzi.com',
            'iat' => time(),
            'exp' => time() + 30 * 60,
            'email' => $email,
            'scopes' => ['auth_verify_email']
        ];
        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public function sendVerificationMail(string $email): bool
    {
        $verifyToken = $this->makeVerifyToken($email);
        $url = $this->router->generate('auth_verify_email');
        return $this->email->send($email, '邮箱验证', 'email_verify', [
            'verify_url' => "$url?verify_token=$verifyToken"
        ]);
    }

    public function decodeJwtToken(string $jwtToken): ?array
    {
        $publicKey = file_get_contents($this->parameterBag->get('app.secret.public'));
        try {
            $decoded = JWT::decode($jwtToken, new Key($publicKey, 'RS256'));
        } catch (Exception) {
            return null;
        }
        return (array)$decoded;
    }

    /**
     * 验证和解析 email verify token 获取 email
     * @param string $verifyToken
     * @param string $routeName
     * @return mixed|null
     */
    public function getEmailByVerifyToken(string $verifyToken, string $routeName): ?string
    {
        $payload = $this->decodeJwtToken($verifyToken);
        if (!$payload || !$payload['email'] || !in_array($routeName, $payload['scopes'])) {
            return null;
        }

        return $payload['email'];
    }
}
