<?php

namespace App\Entity;

use App\Repository\UserStateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserStateRepository::class)]
class UserState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userOption', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 32)]
    private ?string $app_version = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAppVersion(): ?string
    {
        return $this->app_version;
    }

    public function setAppVersion(string $app_version): self
    {
        $this->app_version = $app_version;

        return $this;
    }
}
