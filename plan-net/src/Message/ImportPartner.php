<?php

declare(strict_types=1);

namespace App\Message;

use App\Model\PartnerDTO;

readonly class ImportPartner
{
    public function __construct(private PartnerDTO $partnerDTO, private string $locale)
    {
    }

    public function getPartnerDTO(): PartnerDTO
    {
        return $this->partnerDTO;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
