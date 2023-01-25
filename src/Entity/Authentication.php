<?php

namespace App\Entity;

use App\Config\AuthCredentialType;
use App\Repository\AuthenticationRepository;
use App\Validator\SuppressDuplicateCredential;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_credential_type_key', columns: ['credential_type', 'credential_key'])]
class Authentication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'authentications')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Choice(choices: [
        AuthCredentialType::Email,
        AuthCredentialType::PhoneNumber,
        AuthCredentialType::WechatOpenid,
        AuthCredentialType::QQOpenid
    ])]
    #[ORM\Column(type: Types::SMALLINT, enumType: AuthCredentialType::class)]
    private ?AuthCredentialType $credential_type;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Length(max: 128)]
    #[Assert\When(
        expression: 'this.isCredentialType("Email")',
        constraints: [new Assert\Email(message: '值不是有效的电子邮件地址')]
    )]
    #[SuppressDuplicateCredential(credentialTypeExpr: 'this.getCredentialType()')]
    #[ORM\Column(length: 128)]
    private ?string $credential_key = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $annotation = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    public function isCredentialType(string $key): bool
    {
        $map = [
            'Email' => AuthCredentialType::Email,
            'PhoneNumber' => AuthCredentialType::PhoneNumber,
            'WechatOpenid' => AuthCredentialType::WechatOpenid,
            'QQOpenid' => AuthCredentialType::QQOpenid,
        ];
        if (array_key_exists($key, $map)) {
            return $this->credential_type === $map[$key];
        }
        return false;
    }

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

    public function getCredentialType(): ?AuthCredentialType
    {
        return $this->credential_type;
    }

    public function setCredentialType(?AuthCredentialType $credential_type): self
    {
        $this->credential_type = $credential_type;

        return $this;
    }

    public function getCredentialKey(): ?string
    {
        return $this->credential_key;
    }

    public function setCredentialKey(?string $credential_key): self
    {
        $this->credential_key = $credential_key;

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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function initializeUser(): User
    {
        $user = new User();
        $user->setUniqueId(Uuid::uuid7());
        $user->setNickname($this->getCredentialKey());
        $user->setExp(0);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setCreatedAt($this->getCreatedAt());
        $this->setUser($user);
        return $user;
    }
}
