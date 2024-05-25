<?php

namespace App\MessageHandler;

use App\Entity\Partner;
use App\Message\ImportPartner;
use App\Transformer\PartnerTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ImportPartnerHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    public function __invoke(ImportPartner $importPartner): void
    {
        $partnerDTO = $importPartner->getPartnerDTO();
        $locale = $importPartner->getLocale();

        $partner = new Partner();
        $transformer = new PartnerTransformer($partner, $partnerDTO, $locale);
        $partner = $transformer->transform();

        $this->entityManager->persist($partner);
        $partner->mergeNewTranslations();

        $this->entityManager->flush();
    }
}