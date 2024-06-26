<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Prize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prize>
 *
 * @method Prize|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prize|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prize[]    findAll()
 * @method Prize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prize::class);
    }

    public function countPrizes(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('SUM(p.stock)')
            ->andWhere('p.stock > 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Prize[]
     */
    public function findAvailablePrizes(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stock > 0')
            ->getQuery()
            ->getResult();
    }
}
