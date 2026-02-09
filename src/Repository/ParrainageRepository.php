<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Parrainage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parrainage>
 */
class ParrainageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parrainage::class);
    }

    public function findByEmail(string $email): ?Parrainage
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findByReferralCode(string $code): ?Parrainage
    {
        return $this->findOneBy(['referralCode' => $code]);
    }
}
