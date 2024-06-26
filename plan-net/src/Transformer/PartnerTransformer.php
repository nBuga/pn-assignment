<?php

declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Partner;
use App\Model\PartnerDTO;

readonly class PartnerTransformer
{
    public function __construct(
        private Partner    $partner,
        private PartnerDTO $partnerDTO,
        private string     $locale
    ) {
    }

    public function transform(): Partner
    {
        $this->partner->translate($this->locale, false)->setName($this->partnerDTO->name);
        $this->partner->setUrl($this->partnerDTO->url);
        $this->partner->setCode($this->partnerDTO->code);
        $this->partner->setCode($this->partnerDTO->code);

        return $this->partner;
    }
}
