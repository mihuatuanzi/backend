<?php

namespace App\Entity;

use App\Repository\FormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormRepository::class)]
class Form
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detail = null;

    #[ORM\ManyToOne(inversedBy: 'forms')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormField::class, orphanRemoval: true)]
    private Collection $formFields;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormSubmission::class, orphanRemoval: true)]
    private Collection $formSubmissions;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: DumplingRequirement::class)]
    private Collection $dumplingRequirements;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormValidator::class)]
    private Collection $formValidators;

    public function __construct()
    {
        $this->formFields = new ArrayCollection();
        $this->formSubmissions = new ArrayCollection();
        $this->dumplingRequirements = new ArrayCollection();
        $this->formValidators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

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

    /**
     * @return Collection<int, FormField>
     */
    public function getFormFields(): Collection
    {
        return $this->formFields;
    }

    public function addFormField(FormField $formField): self
    {
        if (!$this->formFields->contains($formField)) {
            $this->formFields->add($formField);
            $formField->setForm($this);
        }

        return $this;
    }

    public function removeFormField(FormField $formField): self
    {
        if ($this->formFields->removeElement($formField)) {
            // set the owning side to null (unless already changed)
            if ($formField->getForm() === $this) {
                $formField->setForm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FormSubmission>
     */
    public function getFormSubmissions(): Collection
    {
        return $this->formSubmissions;
    }

    public function addFormSubmission(FormSubmission $formSubmission): self
    {
        if (!$this->formSubmissions->contains($formSubmission)) {
            $this->formSubmissions->add($formSubmission);
            $formSubmission->setForm($this);
        }

        return $this;
    }

    public function removeFormSubmission(FormSubmission $formSubmission): self
    {
        if ($this->formSubmissions->removeElement($formSubmission)) {
            // set the owning side to null (unless already changed)
            if ($formSubmission->getForm() === $this) {
                $formSubmission->setForm(null);
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
            $dumplingRequirement->setForm($this);
        }

        return $this;
    }

    public function removeDumplingRequirement(DumplingRequirement $dumplingRequirement): self
    {
        if ($this->dumplingRequirements->removeElement($dumplingRequirement)) {
            // set the owning side to null (unless already changed)
            if ($dumplingRequirement->getForm() === $this) {
                $dumplingRequirement->setForm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FormValidator>
     */
    public function getFormValidators(): Collection
    {
        return $this->formValidators;
    }

    public function addFormValidator(FormValidator $formValidator): self
    {
        if (!$this->formValidators->contains($formValidator)) {
            $this->formValidators->add($formValidator);
            $formValidator->setForm($this);
        }

        return $this;
    }

    public function removeFormValidator(FormValidator $formValidator): self
    {
        if ($this->formValidators->removeElement($formValidator)) {
            // set the owning side to null (unless already changed)
            if ($formValidator->getForm() === $this) {
                $formValidator->setForm(null);
            }
        }

        return $this;
    }
}
