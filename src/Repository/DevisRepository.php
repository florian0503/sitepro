<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devis>
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    public function getNextReference(): string
    {
        $year = date('Y');
        $result = $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.reference LIKE :prefix')
            ->setParameter('prefix', 'DEV-'.$year.'-%')
            ->getQuery()
            ->getSingleScalarResult();

        $next = ((int) $result) + 1;

        return \sprintf('DEV-%s-%03d', $year, $next);
    }
}
