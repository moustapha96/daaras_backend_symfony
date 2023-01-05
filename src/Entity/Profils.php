<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProfilsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfilsRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class Profils
{

    #[ORM\Id]
    #[ORM\Column]
    #[Groups(["write", "read"])]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Groups(["write", "read"])]
    private ?string $nom = null;


    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: UserMobile::class)]
    #[Groups(["write"])]
    private Collection $userMobile;


    #[ORM\Column(length: 255)]
    #[Groups(["write", "read"])]
    private ?string $denomination = null;


    public function __construct()
    {

        $this->userMobile = new ArrayCollection();
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


    /**
     * @return Collection<int, UserMobile>
     */
    public function getUserMobile(): Collection
    {
        return $this->userMobile;
    }

    public function addUserMobile(UserMobile $userMobile): self
    {
        if (!$this->userMobile->contains($userMobile)) {
            $this->userMobile->add($userMobile);
            $userMobile->setProfil($this);
        }

        return $this;
    }

    public function removeUserMobile(UserMobile $userMobile): self
    {
        if ($this->userMobile->removeElement($userMobile)) {
            // set the owning side to null (unless already changed)
            if ($userMobile->getProfil() === $this) {
                $userMobile->setProfil(null);
            }
        }

        return $this;
    }


    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'denomination' => $this->denomination
        ];
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }
}