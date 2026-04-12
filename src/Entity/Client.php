<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Client
{
    public const PACK_ESSENTIEL = 'essentiel';
    public const PACK_BUSINESS = 'business';
    public const PACK_ECOMMERCE = 'ecommerce';

    public const SUBSCRIPTION_STARTER = 'starter';
    public const SUBSCRIPTION_CONFORT = 'confort';
    public const SUBSCRIPTION_PREMIUM = 'premium';

    public const SUBSCRIPTION_AMOUNTS = [
        self::SUBSCRIPTION_STARTER => '49.00',
        self::SUBSCRIPTION_CONFORT => '79.00',
        self::SUBSCRIPTION_PREMIUM => '99.00',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 50)]
    private ?string $pack = null;

    #[ORM\Column(length: 50)]
    private ?string $subscription = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $contractStartDate = null;

    #[ORM\Column]
    private int $totalMonths = 24;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $monthlyAmount = '0.00';

    #[ORM\Column]
    private bool $setupFeePaid = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $maintenanceHoursUsed = '0.00';

    #[ORM\Column]
    private bool $hasActiveIssue = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $issueHistory = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->contractStartDate = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getMonthsPaid(): int
    {
        if (null === $this->contractStartDate) {
            return 1;
        }

        $now = new \DateTimeImmutable();
        $diff = $this->contractStartDate->diff($now);
        $months = ($diff->y * 12) + $diff->m + 1;

        return min($this->totalMonths, $months);
    }

    public function getMonthsRemaining(): int
    {
        return max(0, $this->totalMonths - $this->getMonthsPaid());
    }

    public function getTotalPaid(): string
    {
        return bcmul((string) $this->getMonthsPaid(), $this->monthlyAmount, 2);
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getPack(): ?string
    {
        return $this->pack;
    }

    public function setPack(string $pack): static
    {
        $this->pack = $pack;

        return $this;
    }

    public function getSubscription(): ?string
    {
        return $this->subscription;
    }

    public function setSubscription(string $subscription): static
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getContractStartDate(): ?\DateTimeImmutable
    {
        return $this->contractStartDate;
    }

    public function setContractStartDate(\DateTimeImmutable $contractStartDate): static
    {
        $this->contractStartDate = $contractStartDate;

        return $this;
    }

    public function getTotalMonths(): int
    {
        return $this->totalMonths;
    }

    public function setTotalMonths(int $totalMonths): static
    {
        $this->totalMonths = $totalMonths;

        return $this;
    }

    public function getMonthlyAmount(): string
    {
        return $this->monthlyAmount;
    }

    public function setMonthlyAmount(string|float $monthlyAmount): static
    {
        $this->monthlyAmount = (string) $monthlyAmount;

        return $this;
    }

    public function isSetupFeePaid(): bool
    {
        return $this->setupFeePaid;
    }

    public function setSetupFeePaid(bool $setupFeePaid): static
    {
        $this->setupFeePaid = $setupFeePaid;

        return $this;
    }

    public function getMaintenanceHoursUsed(): string
    {
        return $this->maintenanceHoursUsed;
    }

    public function setMaintenanceHoursUsed(string|float $maintenanceHoursUsed): static
    {
        $this->maintenanceHoursUsed = (string) $maintenanceHoursUsed;

        return $this;
    }

    public function isHasActiveIssue(): bool
    {
        return $this->hasActiveIssue;
    }

    public function setHasActiveIssue(bool $hasActiveIssue): static
    {
        $this->hasActiveIssue = $hasActiveIssue;

        return $this;
    }

    public function getIssueHistory(): ?string
    {
        return $this->issueHistory;
    }

    public function setIssueHistory(?string $issueHistory): static
    {
        $this->issueHistory = $issueHistory;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
