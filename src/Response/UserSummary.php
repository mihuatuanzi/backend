<?php

namespace App\Response;

use App\Config\UserGenderType;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class UserSummary
{
    public string $uniqueId;
    public string $nickname;
    public UserGenderType $gender;
    public ?string $avatar;
    public ?string $signature;
    public ?int $createdAt;

    public function withUser(User $user): self
    {
        $this->uniqueId = $user->getUserIdentifier();
        $this->nickname = $user->getNickname();
        $this->gender = $user->getGender();
        $this->avatar = $user->getAvatar();
        $this->signature = $user->getSignature();
        $this->createdAt = $user->getCreatedAt()->getTimestamp();
        return $this;
    }
}
