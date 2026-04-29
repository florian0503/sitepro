<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VilleProspection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VilleProspection>
 */
class VilleProspectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VilleProspection::class);
    }
}
