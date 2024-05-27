<?php

namespace App\MessageHandler;

use App\Entity\Partner;
use App\Message\ImportPartner;
use App\Repository\PartnerRepository;
use App\Transformer\PartnerTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ImportPartnerHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PartnerRepository $partnerRepository,
    ) {
    }
    public function __invoke(ImportPartner $importPartner): void
    {
        $partnerDTO = $importPartner->getPartnerDTO();
        $partner = $this->partnerRepository->findOneBy(['code' => $partnerDTO->code]);
        if (!$partner) {
            $partner = new Partner();
        }

        $transformer = new PartnerTransformer($partner, $partnerDTO, $importPartner->getLocale());
        $partner = $transformer->transform();

        $this->entityManager->persist($partner);
        $partner->mergeNewTranslations();

        $this->entityManager->flush();
    }
}