<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileNotFoundException extends FileException
{
    public function __construct(string $fileName)
    {
        parent::__construct(
            sprintf("Filename %s not found! Check if the locale is correct!", $fileName)
        );
    }
}
