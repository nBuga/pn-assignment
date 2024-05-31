<?php

declare(strict_types=1);

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
