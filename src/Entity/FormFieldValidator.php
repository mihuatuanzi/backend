<?php

namespace App\Entity;

use App\Repository\FormFieldValidatorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormFieldValidatorRepository::class)]
class FormFieldValidator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formFieldValidators')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormValidator $form_validator = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rule = null;

    #[ORM\ManyToOne(inversedBy: 'formFieldValidators')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormField $form_field = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $order_number = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormValidator(): ?FormValidator
    {
        return $this->form_validator;
    }

    public function setFormValidator(?FormValidator $form_validator): self
    {
        $this->form_validator = $form_validator;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(?string $rule): self
    {
        $this->rule = $rule;

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

    public function getFormField(): ?FormField
    {
        return $this->form_field;
    }

    public function setFormField(?FormField $form_field): self
    {
        $this->form_field = $form_field;

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
}
