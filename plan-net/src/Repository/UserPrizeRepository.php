<?php

namespace App\Repository;

use App\Entity\UserPrize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPrize>
 *
 * @method UserPrize|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPrize|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPrize[]    findAll()
 * @method UserPrize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPrizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPrize::class);
    }

    //    /**
    //     * @return UserPrize[] Returns an array of UserPrize objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserPrize
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
