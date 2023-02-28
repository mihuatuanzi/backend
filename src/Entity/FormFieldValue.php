<?php

namespace App\Entity;

use App\Repository\FormFieldValueRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormFieldValueRepository::class)]
class FormFieldValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formFieldValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormSubmission $submission = null;

    #[ORM\ManyToOne(inversedBy: 'formFieldValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormField $field = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubmission(): ?FormSubmission
    {
        return $this->submission;
    }

    public function setSubmission(?FormSubmission $submission): self
    {
        $this->submission = $submission;

        return $this;
    }

    public function getField(): ?FormField
    {
        return $this->field;
    }

    public function setField(?FormField $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
