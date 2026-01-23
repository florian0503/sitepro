<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Realisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Realisation>
 */
class RealisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Realisation::class);
    }

    /**
     * @return Realisation[]
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.isPublished = :published')
            ->setParameter('published', true)
            ->leftJoin('r.category', 'c')
            ->addSelect('c')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Realisation[]
     */
    public function findPublishedByCategory(Category $category): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.isPublished = :published')
            ->andWhere('r.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', $category)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
