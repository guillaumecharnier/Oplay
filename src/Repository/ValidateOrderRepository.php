<?php

namespace App\Repository;

use App\Entity\ValidateOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValidateOrder>
 */
class ValidateOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValidateOrder::class);
    }
        public function findAllWithUser(): array
        {
            return $this->createQueryBuilder('vo')
                ->leftJoin('vo.users', 'u') // Join with user entity
                ->addSelect('u') // Select user entity
                ->getQuery()
                ->getResult();
        }
    //    /**
    //     * @return ValidateOrder[] Returns an array of ValidateOrder objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ValidateOrder
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
