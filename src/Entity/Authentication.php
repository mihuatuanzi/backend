<?php

namespace App\Entity;

use App\Config\AuthCertType;
use App\Repository\AuthenticationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_cert_type_key', columns: ['cert_type', 'cert_key'])]
class Authentication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'authentications')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::SMALLINT, enumType: AuthCertType::class)]
    private AuthCertType $cert_type;

    #[ORM\Column(length: 128)]
    private ?string $cert_key = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $password_hash = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $annotation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCertType(): ?AuthCertType
    {
        return $this->cert_type;
    }

    public function setCertType(AuthCertType $cert_type): self
    {
        $this->cert_type = $cert_type;

        return $this;
    }

    public function getCertKey(): ?string
    {
        return $this->cert_key;
    }

    public function setCertKey(string $cert_key): self
    {
        $this->cert_key = $cert_key;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(?string $password_hash): self
    {
        $this->password_hash = $password_hash;

        return $this;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setAnnotation(?string $annotation): self
    {
        $this->annotation = $annotation;

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
}
