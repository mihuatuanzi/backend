<?php

namespace App\Entity;

use App\Repository\DumplingRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DumplingRepository::class)]
#[ORM\Index(columns: ['title'])]
#[ORM\Index(columns: ['subtitle'])]
class Dumpling
{
    const STATUS_HIDDEN = 0;
    const STATUS_PUBLIC = 1;
    const STATUS_UNSEARCHABLE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dumplings')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detail = null;

    // 0-隐藏 1-公开 2-不展示
    #[Assert\Choice(choices: [
        self::STATUS_HIDDEN,
        self::STATUS_PUBLIC,
        self::STATUS_UNSEARCHABLE
    ])]
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column(length: 255)]
    private ?string $tag = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'dumpling', targetEntity: DumplingApplicant::class, orphanRemoval: true)]
    private Collection $dumplingApplicants;

    #[ORM\OneToMany(mappedBy: 'dumpling', targetEntity: DumplingMember::class, orphanRemoval: true)]
    private Collection $dumplingMembers;

    #[ORM\OneToMany(mappedBy: 'dumpling', targetEntity: DumplingRequirement::class, orphanRemoval: true)]
    private Collection $dumplingRequirements;

    public function __construct()
    {
        $this->dumplingApplicants = new ArrayCollection();
        $this->dumplingMembers = new ArrayCollection();
        $this->dumplingRequirements = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

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

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

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

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, DumplingApplicant>
     */
    public function getDumplingApplicants(): Collection
    {
        return $this->dumplingApplicants;
    }

    public function addDumplingApplicant(DumplingApplicant $dumplingApplicant): self
    {
        if (!$this->dumplingApplicants->contains($dumplingApplicant)) {
            $this->dumplingApplicants->add($dumplingApplicant);
            $dumplingApplicant->setDumpling($this);
        }

        return $this;
    }

    public function removeDumplingApplicant(DumplingApplicant $dumplingApplicant): self
    {
        if ($this->dumplingApplicants->removeElement($dumplingApplicant)) {
            // set the owning side to null (unless already changed)
            if ($dumplingApplicant->getDumpling() === $this) {
                $dumplingApplicant->setDumpling(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DumplingMember>
     */
    public function getDumplingMembers(): Collection
    {
        return $this->dumplingMembers;
    }

    public function addDumplingMember(DumplingMember $dumplingMember): self
    {
        if (!$this->dumplingMembers->contains($dumplingMember)) {
            $this->dumplingMembers->add($dumplingMember);
            $dumplingMember->setDumpling($this);
        }

        return $this;
    }

    public function removeDumplingMember(DumplingMember $dumplingMember): self
    {
        if ($this->dumplingMembers->removeElement($dumplingMember)) {
            // set the owning side to null (unless already changed)
            if ($dumplingMember->getDumpling() === $this) {
                $dumplingMember->setDumpling(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DumplingRequirement>
     */
    public function getDumplingRequirements(): Collection
    {
        return $this->dumplingRequirements;
    }

    public function addDumplingRequirement(DumplingRequirement $dumplingRequirement): self
    {
        if (!$this->dumplingRequirements->contains($dumplingRequirement)) {
            $this->dumplingRequirements->add($dumplingRequirement);
            $dumplingRequirement->setDumpling($this);
        }

        return $this;
    }

    public function removeDumplingRequirement(DumplingRequirement $dumplingRequirement): self
    {
        if ($this->dumplingRequirements->removeElement($dumplingRequirement)) {
            // set the owning side to null (unless already changed)
            if ($dumplingRequirement->getDumpling() === $this) {
                $dumplingRequirement->setDumpling(null);
            }
        }

        return $this;
    }
}
