<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserMobileRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\Entity(repositoryClass: UserMobileRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],

)]
class UserMobile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read", "write"])]
    private $id;

    #[ORM\Column(type: 'json')]
    #[Groups(["read", "write"])]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(["read", "write"])]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $prenom;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $telephone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["read", "write"])]
    private $adresse;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["read", "write"])]
    private $sexe;

    #[ORM\Column(type: 'boolean')]
    #[Groups(["read", "write"])]
    private $enabled;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $password;


    #[ORM\ManyToOne(targetEntity: Region::class)]
    #[Groups(["write"])]
    private $region;


    #[ORM\ManyToOne(targetEntity: Departement::class, cascade: ["persist"])]
    #[Groups(["read", "write"])]
    private $departement;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["read", "write"])]
    private $uuid;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CodeResetPassword::class)]
    private $codeResetPasswords;


    #[ORM\Column(type: 'boolean')]
    #[Groups(["read", "write"])]
    private $hasLaiteries;

    #[ORM\ManyToOne(inversedBy: 'userMobile')]
    #[Groups(["read", "write"])]
    private ?Profils $profil = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["read", "write"])]
    private ?string $localite = null;

    #[ORM\ManyToOne]
    #[Groups(["read", "write"])]
    private ?Status $status = null;

    public function __construct()
    {
        $this->codeResetPasswords = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
    /**
     * @see UserInterface
     */

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;
        return $this;
    }


    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection|CodeResetPassword[]
     */
    public function getCodeResetPasswords(): Collection
    {
        return $this->codeResetPasswords;
    }

    public function addCodeResetPassword(CodeResetPassword $codeResetPassword): self
    {
        if (!$this->codeResetPasswords->contains($codeResetPassword)) {
            $this->codeResetPasswords[] = $codeResetPassword;
            $codeResetPassword->setUser($this);
        }

        return $this;
    }

    public function removeCodeResetPassword(CodeResetPassword $codeResetPassword): self
    {
        if ($this->codeResetPasswords->removeElement($codeResetPassword)) {
            // set the owning side to null (unless already changed)
            if ($codeResetPassword->getUser() === $this) {
                $codeResetPassword->setUser(null);
            }
        }

        return $this;
    }

    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'sexe' => $this->sexe,
            'enabled' => $this->enabled,
            'roles' => $this->roles,
            'region' => $this->region->asArray(),
            'departement' => $this->departement->asArray(),
            'status' => $this->status->asArray(),
            'uuid' => $this->uuid,
            'password' => $this->password,
            'hasLaiteries' => $this->hasLaiteries,
            'localite' => $this->localite,
            'profil' => $this->profil->asArray()
        ];
    }




    public function getHasLaiteries(): ?bool
    {
        return $this->hasLaiteries;
    }
    public function setHasLaiteries(?bool $hasLaiteries): self
    {
        $this->hasLaiteries = $hasLaiteries;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function isHasLaiteries(): ?bool
    {
        return $this->hasLaiteries;
    }



    public function getProfil(): ?Profils
    {
        return $this->profil;
    }

    public function setProfil(?Profils $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): self
    {
        $this->localite = $localite;

        return $this;
    }


    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }
}