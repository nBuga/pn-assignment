<?php

namespace App\Cache;

use App\Repository\PrizeRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CampaignCache
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly PrizeRepository $prizeRepository,
    ) {
    }

    public function countAvailablePrizes(): int
    {
        return $this->cache->get("total-available-prizes", function(ItemInterface $item) {

            /** todo: setExpiresAfter after campaign ends */
            //$item->expiresAfter(5);

            return $this->prizeRepository->countPrizes();
        });
    }
}