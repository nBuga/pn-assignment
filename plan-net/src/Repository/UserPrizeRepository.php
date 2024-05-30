<?php

namespace App\Repository;

use App\Entity\UserPrize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findTodayPrize(UserInterface $user, \DateTime $currentDateTime): ?UserPrize
    {
        return $this->createQueryBuilder('up')
            ->andWhere('up.user = :user')
            ->andWhere('up.receivedPrizeAt LIKE :date')
            ->setParameter('user', $user)
            ->setParameter('date', $currentDateTime->format('Y-m-d').'%')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function countTodayPrizes(\DateTime $currentDateTime): int
    {
        return $this->createQueryBuilder('up')
            ->select('COUNT(up)')
            ->andWhere('up.receivedPrizeAt LIKE :date')
            ->setParameter('date', $currentDateTime->format('Y-m-d').'%')
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }
}
