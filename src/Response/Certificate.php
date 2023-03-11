<?php

namespace App\Response;

use App\Entity\User;
use App\Interface\StructureResponse;
use App\Service\Authentic;

class Certificate implements StructureResponse
{
    const SINGULAR = 'certificate';
    const PLURAL = 'certificates';

    public string $token;
    public string $refresh_token;
    public int $expired_time;
    public int $refresh_time;
    public string $timezone;
    public UserSummary $user_summary;

    public function __construct(
        private readonly Authentic   $authentic,
        private readonly UserSummary $userSummary
    )
    {
    }

    public function withUser(User $user): self
    {
        [$accessToken, $refreshToken, $expiredTime, $refreshTime] = $this->authentic->makeAccessToken($user);
        $this->token = $accessToken;
        $this->refresh_token = $refreshToken;
        $this->expired_time = $expiredTime;
        $this->refresh_time = $refreshTime;
        $this->timezone = 'UTC';
        $this->{UserSummary::SINGULAR} = $this->userSummary->withUser($user);
        return $this;
    }
}
