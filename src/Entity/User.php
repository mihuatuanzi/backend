<?php

namespace App\Entity;

use App\Config\UserGenderType;
use App\Config\UserType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private const AVATAR_BASE_URL = 'https://mihuatuanzi-backend.oss-cn-hangzhou.aliyuncs.com/';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Uuid]
    #[ORM\Column(length: 64, unique: true)]
    private ?string $unique_id = null;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Choice(choices: [
        UserType::Person,
        UserType::Bot,
        UserType::Client
    ], message: '用户类型不正确')]
    #[ORM\Column(type: Types::SMALLINT, enumType: UserType::class)]
    private UserType $type = UserType::Person;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $password = null;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Length(max: 64, maxMessage: '昵称字数过多')]
    #[ORM\Column(length: 64)]
    private ?string $nickname = null;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Choice(choices: [
        UserGenderType::Female,
        UserGenderType::Male,
        UserGenderType::Neutral
    ], message: '性别类型不正确')]
    #[ORM\Column(type: Types::SMALLINT, enumType: UserGenderType::class)]
    private UserGenderType $gender = UserGenderType::Neutral;

    #[Assert\Length(max: 255, maxMessage: '头像地址过长')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[Assert\Length(max: 255, maxMessage: '签名超出最大长度')]
    #[ORM\Column(length: 255)]
    private string $signature = '';

    #[Assert\NotBlank(message: '值不能为空')]
    #[ORM\Column]
    private ?int $exp = null;

    #[Assert\NotBlank(message: '值不能为空')]
    #[Assert\Choice(choices: [self::STATUS_ACTIVE, self::STATUS_INACTIVE], message: '用户状态不正确')]
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Authentication::class, orphanRemoval: true)]
    private Collection $authentications;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Dumpling::class, orphanRemoval: true)]
    private Collection $dumplings;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserState $userState = null;

    public function __construct()
    {
        $this->authentications = new ArrayCollection();
        $this->dumplings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqueId(): ?string
    {
        return $this->unique_id;
    }

    public function setUniqueId(string $uniqueId): self
    {
        $this->unique_id = $uniqueId;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->unique_id;
    }

    public function getType(): ?UserType
    {
        return $this->type;
    }

    public function setType(UserType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
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

    public function getGender(): ?UserGenderType
    {
        return $this->gender;
    }

    public function setGender(UserGenderType $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar ? self::AVATAR_BASE_URL . $this->avatar : null;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function setExp(int $exp): self
    {
        $this->exp = $exp;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Authentication>
     */
    public function getAuthentications(): Collection
    {
        return $this->authentications;
    }

    public function addAuthentication(Authentication $authentication): self
    {
        if (!$this->authentications->contains($authentication)) {
            $this->authentications->add($authentication);
            $authentication->setUser($this);
        }

        return $this;
    }

    public function removeAuthentication(Authentication $authentication): self
    {
        if ($this->authentications->removeElement($authentication)) {
            // set the owning side to null (unless already changed)
            if ($authentication->getUser() === $this) {
                $authentication->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dumpling>
     */
    public function getDumplings(): Collection
    {
        return $this->dumplings;
    }

    public function addDumpling(Dumpling $dumpling): self
    {
        if (!$this->dumplings->contains($dumpling)) {
            $this->dumplings->add($dumpling);
            $dumpling->setUser($this);
        }

        return $this;
    }

    public function removeDumpling(Dumpling $dumpling): self
    {
        if ($this->dumplings->removeElement($dumpling)) {
            // set the owning side to null (unless already changed)
            if ($dumpling->getUser() === $this) {
                $dumpling->setUser(null);
            }
        }

        return $this;
    }

    public function getUserState(): ?UserState
    {
        return $this->userState;
    }

    public function setUserState(UserState $userState): self
    {
        // set the owning side of the relation if necessary
        if ($userState->getUser() !== $this) {
            $userState->setUser($this);
        }

        $this->userState = $userState;

        return $this;
    }
}
