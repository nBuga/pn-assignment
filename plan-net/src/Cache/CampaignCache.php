<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\PrizeRepository;
use App\Service\CampaignService;
use Symfony\Contracts\Cache\CacheInterface;

readonly class CampaignCache
{
    public const PRIZES_PER_DAY = "prizes-per-day";
    public const PRIZES_FOR_DATE = "prizes-for-date";

    public function __construct(
        private CacheInterface $cache,
        private PrizeRepository $prizeRepository,
    ) {
    }

    public function setCampaignPrizesPerDay(): void
    {
        $prizesPerDay = $this->getPrizesPerDay();

        $startDateTime = CampaignService::startDateTime();
        $endDateTime = CampaignService::endDateTime();

        $keyStartDate = self::PRIZES_FOR_DATE.'-'.$startDateTime->format('Y-m-d');
        $keyEndDate = self::PRIZES_FOR_DATE.'-'.$endDateTime->format('Y-m-d');

        $this->cache->get($keyStartDate, function () use ($prizesPerDay) {
            return $prizesPerDay;
        });

        $this->cache->get($keyEndDate, function () use ($prizesPerDay) {
            return $prizesPerDay;
        });
    }

    public function getPrizesPerDay(): int
    {
        return $this->cache->get(self::PRIZES_PER_DAY, function () {
            $totalAvailablePrizes = $this->prizeRepository->countPrizes();

            return (int) round(num: $totalAvailablePrizes / CampaignService::CAMPAIGN_AVAILABILITY_DAYS, mode: PHP_ROUND_HALF_DOWN);
        });
    }
}
