<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DepartementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],

)]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read", "write"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $nom;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'departement')]
    #[Groups(["read", "write"])]
    private $region;

    #[ORM\ManyToOne(targetEntity: Zones::class, inversedBy: 'departements')]
    #[Groups(["read", "write"])]
    protected $zones;


    // #[ORM\OneToMany(mappedBy: 'departement', targetEntity: Laiterie::class)]
    // #[Groups(["write"])]
    // public $laiteries;

    // #[ORM\ManyToOne(targetEntity: Region::class )]
    // #[Groups(["read", "write"])]
    // #[ORM\JoinColumn(nullable: false)]
    // private $region;




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

    // public function getRegion(): ?Region
    // {
    //     return $this->region;
    // }

    // public function setRegion(?Region $region): self
    // {
    //     $this->region = $region;

    //     return $this;
    // }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getZones(): ?Zones
    {
        return $this->zones;
    }

    public function setZones(?Zones $zones): self
    {
        $this->zones = $zones;

        return $this;
    }

    public function asArray(): ?array
    {
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "region" => $this->region->asArray(),
            "zones" => $this->zones
        ];
    }
}