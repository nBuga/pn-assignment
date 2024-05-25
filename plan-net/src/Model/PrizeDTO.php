<?php

namespace App\Model;

class PrizeDTO
{
    public function __construct(
        public string $partner_code,
        public string $name,
        public string $description,
        public string $code
    ) {
    }
}