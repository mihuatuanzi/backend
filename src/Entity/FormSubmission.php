<?php

namespace App\Entity;

use App\Repository\FormSubmissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormSubmissionRepository::class)]
class FormSubmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formSubmissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Form $form = null;

    #[ORM\ManyToOne(inversedBy: 'formSubmissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarks = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'submission', targetEntity: FormFieldValue::class, orphanRemoval: true)]
    private Collection $formFieldValues;

    public function __construct()
    {
        $this->formFieldValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): self
    {
        $this->form = $form;

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

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

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
     * @return Collection<int, FormFieldValue>
     */
    public function getFormFieldValues(): Collection
    {
        return $this->formFieldValues;
    }

    public function addFormFieldValue(FormFieldValue $formFieldValue): self
    {
        if (!$this->formFieldValues->contains($formFieldValue)) {
            $this->formFieldValues->add($formFieldValue);
            $formFieldValue->setSubmission($this);
        }

        return $this;
    }

    public function removeFormFieldValue(FormFieldValue $formFieldValue): self
    {
        if ($this->formFieldValues->removeElement($formFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($formFieldValue->getSubmission() === $this) {
                $formFieldValue->setSubmission(null);
            }
        }

        return $this;
    }
}
