<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\Authentic;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * {@see https://symfony.com/doc/current/security/access_token.html}
 */
readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private Authentic      $authentic
    )
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $payload = $this->authentic->decodeJwtToken($accessToken);
//        $payload = ['sub' => '0185ddde-eee1-700d-8ec3-f6862cbcf521'];
        if (!$payload) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $uniqueId = $payload['sub'];
        $count = $this->userRepository->count(['unique_id' => $uniqueId]);
        if ($count === 0) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($uniqueId);
    }
}
