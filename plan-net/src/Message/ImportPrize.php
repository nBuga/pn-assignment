<?php

declare(strict_types=1);

namespace App\Message;

use App\Model\PrizeDTO;

readonly class ImportPrize
{
    public function __construct(private PrizeDTO $prizeDTO, private string $locale)
    {
    }

    public function getPrizeDTO(): PrizeDTO
    {
        return $this->prizeDTO;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
