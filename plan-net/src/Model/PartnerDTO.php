<?php

namespace App\Model;

class PartnerDTO
{
    public function __construct(
        public string $name,
        public string $url,
        public string $code
    ) {
    }
}