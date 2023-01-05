<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ModuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    order: ['position' => 'ASC'],
)]

#[UniqueEntity(fields: ['position'], message: 'cette position est deja prise')]
#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["read", "write"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["read", "write"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["read", "write"])]
    private ?string $icone = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups(["read", "write"])]
    private array $formulaire = [];

    #[ORM\Column(length: 255)]
    #[Groups(["read", "write"])]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["read", "write"])]
    private ?string $logo = null;

    #[ORM\Column(nullable: true, unique: true)]
    #[Groups(["read", "write"])]
    private ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }
    public function setLogo(string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getFormulaire(): array
    {
        return $this->formulaire;
    }

    public function setFormulaire(?array $formulaire): self
    {
        $this->formulaire = $formulaire;

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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}