<?php

namespace App\Transformer;

use App\Entity\Partner;
use App\Entity\Prize;
use App\Model\PrizeDTO;

readonly class PrizeTransformer
{
    public function __construct(
        private Prize    $prize,
        private PrizeDTO $prizeDTO,
        private string     $locale
    ){
    }

    public function transform(Partner $partner): Prize
    {
        $this->prize->setPartner($partner);
        $this->prize->setCode($this->prizeDTO->code);
        $this->prize->setStock($this->prize->getStock() + 1);
        $this->prize->translate($this->locale, false)->setName($this->prizeDTO->name);
        $this->prize->translate($this->locale, false)->setDescription($this->prizeDTO->description);

        return $this->prize;
    }
}