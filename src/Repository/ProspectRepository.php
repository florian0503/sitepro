<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Prospect;
use App\Entity\VilleProspection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prospect>
 */
class ProspectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prospect::class);
    }

    public function findNextPosition(VilleProspection $ville): int
    {
        $result = $this->createQueryBuilder('p')
            ->select('MAX(p.position)')
            ->where('p.ville = :ville')
            ->setParameter('ville', $ville)
            ->getQuery()
            ->getSingleScalarResult();

        return (null === $result) ? 0 : (int) $result + 1;
    }
}
