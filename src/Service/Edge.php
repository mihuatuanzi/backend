<?php

namespace App\Service;

use App\Interface\EmailDelivery;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class Edge
{
    public function __construct(
        private EmailDelivery         $email,
        private UrlGeneratorInterface $router,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    public function sendVerificationMail(string $email): bool
    {
        $privateKey = file_get_contents($this->parameterBag->get('app.secret.private'));
        $payload = [
            'iss' => 'mihuatuanzi.com',
            'aud' => 'mihuatuanzi.com',
            'iat' => time(),
            'exp' => time() + 30 * 60,
            'email' => $email
        ];
        $verifyToken = JWT::encode($payload, $privateKey, 'RS256');

        $url = $this->router->generate('app_user_signup_by_email');
        return $this->email->send($email, '邮箱验证', 'email_verify', [
            'verify_url' => "$url?verify_token=$verifyToken&t=" . time()
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
        return (array) $decoded;
    }
}
