<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CandidatureRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
#[ApiResource]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getCandidatures"])]

    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["getCandidatures"])]
    private ?bool $statut = null;
    

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[Groups(["getCandidatures"])]
    private ?User $User = null;
   

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[Groups(["getCandidatures"])]
    private ?Formation $Formation = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->Formation;
    }

    public function setFormation(?Formation $Formation): static
    {
        $this->Formation = $Formation;

        return $this;
    }
}
