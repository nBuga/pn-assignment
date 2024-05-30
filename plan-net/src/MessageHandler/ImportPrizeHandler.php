<?php

namespace App\MessageHandler;

use App\Entity\Prize;
use App\Exception\PartnerNotFoundException;
use App\Message\ImportPrize;
use App\Repository\PartnerRepository;
use App\Repository\PrizeRepository;
use App\Transformer\PrizeTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ImportPrizeHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PartnerRepository $partnerRepository,
        private PrizeRepository $prizeRepository,
    ) {
    }

    /**
     * @throws PartnerNotFoundException
     */
    public function __invoke(ImportPrize $importPrize): void
    {
        $prizeDTO = $importPrize->getPrizeDTO();
        $partner = $this->partnerRepository->findOneBy(['code' => $prizeDTO->partner_code]);
        if (!$partner) {
            throw new PartnerNotFoundException($prizeDTO->partner_code);
        }

        $prize = $this->prizeRepository->findOneBy(['partner' => $partner, 'code' => $prizeDTO->code]);
        if (!$prize) {
            $prize = new Prize();
        }

        $transformer = new PrizeTransformer($prize, $prizeDTO, $importPrize->getLocale());
        $prize = $transformer->transform($partner);

        $this->entityManager->persist($prize);
        $prize->mergeNewTranslations();

        $this->entityManager->flush();
    }
}