<?php

namespace App\Entity;

use App\Config\UserGenderType;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true)]
    private ?string $unique_id = null;

    #[ORM\Column(length: 64)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: UserGenderType::class)]
    private UserGenderType $gender;

    #[ORM\Column(length: 255)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255)]
    private ?string $signature = '';

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Authentication::class, orphanRemoval: true)]
    private Collection $authentications;

    public function __construct()
    {
        $this->authentications = new ArrayCollection();
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
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
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
}
