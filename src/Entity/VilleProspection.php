<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VilleProspectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleProspectionRepository::class)]
class VilleProspection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    #[ORM\Column(length: 10)]
    private ?string $codePostal = null;

    /** @var Collection<int, Prospect> */
    #[ORM\OneToMany(targetEntity: Prospect::class, mappedBy: 'ville', cascade: ['remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $prospects;

    public function __construct()
    {
        $this->prospects = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom.' ('.$this->codePostal.')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /** @return Collection<int, Prospect> */
    public function getProspects(): Collection
    {
        return $this->prospects;
    }

    public function addProspect(Prospect $prospect): static
    {
        if (!$this->prospects->contains($prospect)) {
            $this->prospects->add($prospect);
            $prospect->setVille($this);
        }

        return $this;
    }

    public function removeProspect(Prospect $prospect): static
    {
        if ($this->prospects->removeElement($prospect)) {
            if ($prospect->getVille() === $this) {
                $prospect->setVille(null);
            }
        }

        return $this;
    }

    public function getNombreProspects(): int
    {
        return $this->prospects->count();
    }
}
