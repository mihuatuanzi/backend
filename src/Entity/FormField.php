<?php

namespace App\Entity;

use App\Config\FormFieldType;
use App\Repository\FormFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\ParameterBag;

#[ORM\Entity(repositoryClass: FormFieldRepository::class)]
class FormField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Form $form = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detail = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: FormFieldType::class)]
    private ?FormFieldType $type = null;

    #[ORM\Column]
    private ?int $order_number = null;

    #[ORM\Column(nullable: true)]
    private ?array $annotation = null;

    #[ORM\OneToMany(mappedBy: 'field', targetEntity: FormFieldValue::class, orphanRemoval: true)]
    private Collection $formFieldValues;

    #[ORM\OneToMany(mappedBy: 'form_field', targetEntity: FormFieldValidator::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $formFieldValidators;

    public function __construct()
    {
        $this->formFieldValues = new ArrayCollection();
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getType(): FormFieldType
    {
        return $this->type;
    }

    public function setType(FormFieldType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->order_number;
    }

    public function setOrderNumber(int $order_number): self
    {
        $this->order_number = $order_number;

        return $this;
    }

    public function getAnnotation(): array
    {
        return $this->annotation;
    }

    public function setAnnotation(?array $annotation): self
    {
        $this->annotation = $annotation;

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
            $formFieldValue->setField($this);
        }

        return $this;
    }

    public function removeFormFieldValue(FormFieldValue $formFieldValue): self
    {
        if ($this->formFieldValues->removeElement($formFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($formFieldValue->getField() === $this) {
                $formFieldValue->setField(null);
            }
        }

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
            $formFieldValidator->setFormField($this);
        }

        return $this;
    }

    public function removeFormFieldValidator(FormFieldValidator $formFieldValidator): self
    {
        if ($this->formFieldValidators->removeElement($formFieldValidator)) {
            // set the owning side to null (unless already changed)
            if ($formFieldValidator->getFormField() === $this) {
                $formFieldValidator->setFormField(null);
            }
        }

        return $this;
    }

    public function loadFromParameterBag(ParameterBag $bag): self
    {
        $this->setLabel($bag->get('label'));
        $this->setDetail($bag->get('detail'));
        $this->setType(FormFieldType::from($bag->get('type')));
        $this->setAnnotation($bag->get('annotation'));
        $this->setOrderNumber(0);
        return $this;
    }
}
