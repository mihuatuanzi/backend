<?php

namespace App\Entity;

use App\Repository\DumplingMemberRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\UniqueConstraint(columns: ['dumpling_id', 'user_id'])]
#[ORM\Entity(repositoryClass: DumplingMemberRepository::class)]
class DumplingMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dumplingMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dumpling $dumpling = null;

    #[ORM\ManyToOne(inversedBy: 'dumplingMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Length(max: 64, maxMessage: '昵称字数过多')]
    #[ORM\Column(length: 64)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $status_mask = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDumpling(): ?Dumpling
    {
        return $this->dumpling;
    }

    public function setDumpling(?Dumpling $dumpling): self
    {
        $this->dumpling = $dumpling;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_MEMBER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getStatusMask(): ?string
    {
        return $this->status_mask;
    }

    public function setStatusMask(string $status_mask): self
    {
        $this->status_mask = $status_mask;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function initialProperty(): self
    {
        $this->setStatusMask(0);
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setUpdatedAt(new DateTime());
        return $this;
    }
}
