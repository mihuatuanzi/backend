<?php

namespace App\Entity;

use App\Repository\FormValidatorRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\ParameterBag;

#[ORM\Entity(repositoryClass: FormValidatorRepository::class)]
class FormValidator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formValidators')]
    private ?Form $form = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarks = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'form_validator', targetEntity: FormFieldValidator::class, orphanRemoval: true)]
    private Collection $formFieldValidators;

    #[ORM\ManyToOne(inversedBy: 'formValidators')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->formFieldValidators = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
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
     * @return Collection<int, FormFieldValidator>
     */
    public function getFormFieldValidators(): Collection
    {
        return $this->formFieldValidators;
    }

    public function addFormFieldValidator(FormFieldValidator $formFieldValidator): self
    {
        if (!$this->formFieldValidators->contains($formFieldValidator)) {
            $this->formFieldValidators->add($formFieldValidator);
            $formFieldValidator->setFormValidator($this);
        }

        return $this;
    }

    public function removeFormFieldValidator(FormFieldValidator $formFieldValidator): self
    {
        if ($this->formFieldValidators->removeElement($formFieldValidator)) {
            // set the owning side to null (unless already changed)
            if ($formFieldValidator->getFormValidator() === $this) {
                $formFieldValidator->setFormValidator(null);
            }
        }

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

    public function loadFromParameterBag(ParameterBag $bag): self
    {
        $this->setName($bag->get('title'));
        $this->setRemarks('');
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setUpdatedAt(new DateTime());
        return $this;
    }
}
