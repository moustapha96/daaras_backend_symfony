<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CodeResetPasswordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: CodeResetPasswordRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class CodeResetPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read", "write"])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Groups(["read", "write"])]
    private $code;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(["read", "write"])]
    private $dateCreateAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["read", "write"])]
    private $dateExpirate;

    #[ORM\ManyToOne(targetEntity: UserMobile::class, inversedBy: 'codeResetPasswords')]
    #[Groups(["read", "write"])]
    private $user;

    #[ORM\Column(type: 'boolean')]
    #[Groups(["read", "write"])]
    private $enable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDateCreateAt(): ?\DateTimeImmutable
    {
        return $this->dateCreateAt;
    }

    public function setDateCreateAt(\DateTimeImmutable $dateCreateAt): self
    {
        $this->dateCreateAt = $dateCreateAt;

        return $this;
    }

    public function getDateExpirate(): ?\DateTimeInterface
    {
        return $this->dateExpirate;
    }

    public function setDateExpirate(?\DateTimeInterface $dateExpirate): self
    {
        $this->dateExpirate = $dateExpirate;

        return $this;
    }

    public function getUser(): ?UserMobile
    {
        return $this->user;
    }

    public function setUser(?UserMobile $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {

        if ($this->dateCreateAt >= $this->dateExpirate) {
            $this->enable = false;
        }
        $this->enable = $enable;
        return $this;
    }

    public function isEnable(): ?bool
    {
        return $this->enable;
    }
}