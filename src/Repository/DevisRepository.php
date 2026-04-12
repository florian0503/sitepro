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
        $prefix = 'DEV-'.$year.'-';

        $result = $this->createQueryBuilder('d')
            ->select('MAX(d.reference)')
            ->where('d.reference LIKE :prefix')
            ->setParameter('prefix', $prefix.'%')
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return $prefix.'001';
        }

        $lastNumber = (int) substr($result, \strlen($prefix));

        return \sprintf('DEV-%s-%03d', $year, $lastNumber + 1);
    }
}
