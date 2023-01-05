<?php

// ini_set('memory_limit', '1024M'); // or you could use 1G

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ZonesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ZonesRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],

)]
class Zones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read", "write"])]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    public $nom;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["read", "write"])]
    public $description;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(["read", "write"])]
    public $statut;

    #[ORM\OneToMany(mappedBy: 'zones', targetEntity: Departement::class)]
    #[Groups(["write"])]
    public $departements;


    public function __construct()
    {
        $this->departements = new ArrayCollection();
    }


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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection|Departement[]
     */
    public function getDepartements(): Collection
    {
        return $this->departements;
    }

    public function addDepartement(Departement $departement): self
    {
        if (!$this->departements->contains($departement)) {
            $this->departements[] = $departement;
            $departement->setZones($this);
        }

        return $this;
    }

    public function removeDepartement(Departement $departement): self
    {
        if ($this->departements->removeElement($departement)) {
            // set the owning side to null (unless already changed)
            if ($departement->getZones() === $this) {
                $departement->setZones(null);
            }
        }

        return $this;
    }


    public function __toString()
    {
        return (string) $this->departements;
    }
    public function asArray(): ?array
    {

        $resultatsD = array();
        foreach ($this->departements as $d) {
            $resultatsD[] = $d->asArray();
        }

        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "statut" => $this->statut,
            "departements" => $resultatsD,
            // "laiteries" => $this->laiteries,
        ];
    }
    public function asArraygetDepartement(): ?array
    {


        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "statut" => $this->statut,
        ];
    }
    public function asArraygetWithOutDepartement(): ?array
    {


        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "statut" => $this->statut,
        ];
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }
}