<?php
declare(strict_types=1);

namespace App\Exception;

class PartnerNotFoundException extends \Exception
{
    public function __construct(string $partnerCode)
    {
        parent::__construct(
            sprintf("Partner with code %s not found! First, you must import the partners!", $partnerCode)
        );
    }
}