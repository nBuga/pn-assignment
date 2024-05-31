<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Prize;
use App\Entity\UserPrize;
use App\Repository\PrizeRepository;
use App\Repository\UserPrizeRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PrizeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PrizeRepository $prizeRepository,
        private UserPrizeRepository $userPrizeRepository,
    ) {
    }

    public function saveUserPrize(UserInterface $user): Prize
    {
        $prize = $this->getRandomPrize();
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $userPrize = new UserPrize();
            $userPrize->setPrize($prize);
            $userPrize->setUser($user);
            $userPrize->setReceivedPrizeAt(CampaignService::currentDateTime());

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

    private function getRandomPrize(): Prize
    {
        $prizes = $this->prizeRepository->findAvailablePrizes();
        shuffle($prizes);

        return $prizes[0];
    }

    public function redeemTodayPrize(UserInterface $user): ?Prize
    {
        $userPrize = $this->userPrizeRepository->findTodayPrize($user, CampaignService::currentDateTime());

        return $userPrize?->getPrize() ?? null;
    }
}
