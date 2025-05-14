<?php

namespace App\Repository;

use App\Entity\Recept;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recept>
 */
class ReceptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recept::class);
    }

    public function findAllOrderedByCategory(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.category', 'ASC')
            ->addOrderBy('r.type', 'ASC')
            ->addOrderBy('r.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Recept[] Returns an array of Recept objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recept
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
