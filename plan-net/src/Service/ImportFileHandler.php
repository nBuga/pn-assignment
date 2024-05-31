<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FileNotFoundException;

class ImportFileHandler
{
    public function getFileContentLines(string $filename, string $locale, string $separator = ','): ?\Generator
    {
        $header = [];
        $row = 0;

        $fullPath = $this->getFullPath($filename, $locale);
        if (!file_exists($fullPath)) {
            throw new FileNotFoundException($fullPath);
        }

        $handle = fopen($fullPath, "r");

        if ($handle === false) {
            return null;
        }

        while (($data = fgetcsv($handle, 0, $separator)) !== false) {
            $data = array_map('trim', $data);
            if (0 == $row) {
                $header = $data;
            } else {
                yield array_combine($header, $data);
            }

            $row++;
        }
        fclose($handle);
    }

    private function getFullPath(string $filename, string $locale): string
    {
        return vsprintf('%s%s%s_%s.%s', [
            'public/attachments',
            DIRECTORY_SEPARATOR,
            ltrim($filename, DIRECTORY_SEPARATOR),
            strtolower($locale),
            'csv'
        ]);
    }


}
