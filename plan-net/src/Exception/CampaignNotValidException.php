<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class CampaignNotValidException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct(message: $message, code: Response::HTTP_OK);
    }
}