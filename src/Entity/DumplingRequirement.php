<?php

namespace App\Entity;

use App\Repository\DumplingRequirementRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DumplingRequirementRepository::class)]
class DumplingRequirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Valid]
    #[ORM\ManyToOne(cascade: ['persist', 'detach'], inversedBy: 'dumplingRequirements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Form $form = null;

    #[ORM\ManyToOne(inversedBy: 'dumplingRequirements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dumpling $dumpling = null;

    #[ORM\ManyToOne]
    private ?FormValidator $form_validator = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

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

    public function getDumpling(): ?Dumpling
    {
        return $this->dumpling;
    }

    public function setDumpling(?Dumpling $dumpling): self
    {
        $this->dumpling = $dumpling;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function loadFromParameterBag(ParameterBag $bag): self
    {
        $this->setName($bag->get('name'));
        $this->setStatus($bag->get('status'));
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setUpdatedAt(new DateTime());
        return $this;
    }
}
