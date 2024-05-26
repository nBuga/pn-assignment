<?php

namespace App\Exception;

class CampaignNotValidException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}