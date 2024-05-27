<?php

namespace App\Service;

use App\Entity\Prize;
use App\Entity\UserPrize;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PrizeService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function saveUserPrize(UserInterface $user, Prize $prize): Prize
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $userPrize = new UserPrize();
            $userPrize->setPrize($prize);
            $userPrize->setUser($user);
            $userPrize->setReceivedPrizeAt(new \DateTime('now', new \DateTimeZone(CampaignService::TIMEZONE)));

            $this->entityManager->persist($userPrize);
            $this->entityManager->flush();

            $this->updatePrizeStock($prize);
            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }

        return $prize;
    }

    private function updatePrizeStock(Prize $prize): void
    {
        $prize->setStock($prize->getStock() - 1);
        $this->entityManager->flush();
    }
}