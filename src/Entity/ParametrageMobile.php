<?php

namespace App\Entity;

use App\Repository\ParametrageMobileRepository;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\DBAL\Types\Types;

#[ApiResource]
#[ORM\Entity(repositoryClass: ParametrageMobileRepository::class)]
class ParametrageMobile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $urlProd = null;

    #[ORM\Column(length: 255)]
    private ?string $urlDemo = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\Column]
    private ?bool $hasNotification = null;


    #[ORM\Column(length: 255)]
    private ?string $primary_color = null;

    #[ORM\Column(length: 255)]
    private ?string $secondary_color = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    private ?string $tertiary_color = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $logoStructure = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlProd(): ?string
    {
        return $this->urlProd;
    }

    public function setUrlProd(string $urlProd): self
    {
        $this->urlProd = $urlProd;
        return $this;
    }

    public function getUrlDemo(): ?string
    {
        return $this->urlDemo;
    }

    public function setUrlDemo(string $urlDemo): self
    {
        $this->urlDemo = $urlDemo;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function isHasNotification(): ?bool
    {
        return $this->hasNotification;
    }

    public function setHasNotification(bool $hasNotification): self
    {
        $this->hasNotification = $hasNotification;

        return $this;
    }



    public function getPrimaryColor(): ?string
    {
        return $this->primary_color;
    }

    public function setPrimaryColor(string $primary_color): self
    {
        $this->primary_color = $primary_color;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondary_color;
    }

    public function setSecondaryColor(string $secondary_color): self
    {
        $this->secondary_color = $secondary_color;

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getTertiaryColor(): ?string
    {
        return $this->tertiary_color;
    }

    public function setTertiaryColor(string $tertiary_color): self
    {
        $this->tertiary_color = $tertiary_color;

        return $this;
    }

    public function getLogoStructure(): ?string
    {
        return $this->logoStructure;
    }

    public function setLogoStructure(string $logoStructure): self
    {
        $this->logoStructure = $logoStructure;

        return $this;
    }
}