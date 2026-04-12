<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 100)]
    private ?string $clientFirstName = null;

    #[ORM\Column(length: 100)]
    private ?string $clientLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $clientEmail = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $clientPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientCompany = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientAddress = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $clientSiret = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalHt = '0.00';

    #[ORM\Column]
    private int $validityDays = 7;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, DevisItem> */
    #[ORM\OneToMany(targetEntity: DevisItem::class, mappedBy: 'devis', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->reference ?? 'Nouveau devis';
    }

    public function computeTotals(): void
    {
        $ht = '0.00';
        foreach ($this->items as $item) {
            if (!$item->isMonthly()) {
                $ht = bcadd($ht, $item->getPrice(), 2);
            }
        }
        $this->totalHt = $ht;
    }

    public function getMonthlyTotal(): string
    {
        $total = '0.00';
        foreach ($this->items as $item) {
            if ($item->isMonthly()) {
                $total = bcadd($total, $item->getPrice(), 2);
            }
        }

        return $total;
    }

    public function getClientFullName(): string
    {
        return $this->clientFirstName.' '.$this->clientLastName;
    }

    public function getValidUntil(): \DateTimeImmutable
    {
        return $this->createdAt->modify('+'.$this->validityDays.' days');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getClientFirstName(): ?string
    {
        return $this->clientFirstName;
    }

    public function setClientFirstName(string $clientFirstName): static
    {
        $this->clientFirstName = $clientFirstName;

        return $this;
    }

    public function getClientLastName(): ?string
    {
        return $this->clientLastName;
    }

    public function setClientLastName(string $clientLastName): static
    {
        $this->clientLastName = $clientLastName;

        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): static
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): static
    {
        $this->clientPhone = $clientPhone;

        return $this;
    }

    public function getClientCompany(): ?string
    {
        return $this->clientCompany;
    }

    public function setClientCompany(?string $clientCompany): static
    {
        $this->clientCompany = $clientCompany;

        return $this;
    }

    public function getClientAddress(): ?string
    {
        return $this->clientAddress;
    }

    public function setClientAddress(?string $clientAddress): static
    {
        $this->clientAddress = $clientAddress;

        return $this;
    }

    public function getClientSiret(): ?string
    {
        return $this->clientSiret;
    }

    public function setClientSiret(?string $clientSiret): static
    {
        $this->clientSiret = $clientSiret;

        return $this;
    }

    public function getTotalHt(): string
    {
        return $this->totalHt;
    }

    public function setTotalHt(string|float $totalHt): static
    {
        $this->totalHt = (string) $totalHt;

        return $this;
    }

    public function getValidityDays(): int
    {
        return $this->validityDays;
    }

    public function setValidityDays(int $validityDays): static
    {
        $this->validityDays = $validityDays;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @return Collection<int, DevisItem> */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(DevisItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setDevis($this);
        }

        return $this;
    }

    public function removeItem(DevisItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getDevis() === $this) {
                $item->setDevis(null);
            }
        }

        return $this;
    }
}
