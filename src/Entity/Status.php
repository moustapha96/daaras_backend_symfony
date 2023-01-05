<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
#[ApiResource (
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    
)]
class Status
{
    #[ORM\Id]
    // #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["read", "write"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["read", "write"])]
    private ?string $nom = null;

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
    public function asArray(): array{
        return [
            'id'=> $this->id,
            'nom'=> $this->nom
        ];
    }
}
