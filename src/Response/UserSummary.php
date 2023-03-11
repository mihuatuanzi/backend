<?php

namespace App\Response;

use App\Config\UserGenderType;
use App\Entity\User;
use App\Interface\StructureResponse;

class UserSummary implements StructureResponse
{
    const SINGULAR = 'user_summary';
    const PLURAL = 'user_summaries';

    public string $uniqueId;
    public string $nickname;
    public UserGenderType $gender;
    public ?string $avatar;
    public ?string $signature;
    public ?int $created_at;

    public function withUser(User $user): self
    {
        $this->uniqueId = $user->getUserIdentifier();
        $this->nickname = $user->getNickname();
        $this->gender = $user->getGender();
        $this->avatar = $user->getAvatar();
        $this->signature = $user->getSignature();
        $this->created_at = $user->getCreatedAt()->getTimestamp();
        return $this;
    }
}
