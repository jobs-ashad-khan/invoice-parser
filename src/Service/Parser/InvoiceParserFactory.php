<?php

declare(strict_types=1);

namespace App\Service\Parser;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

final class InvoiceParserFactory
{
    public static function create(string $path): InvoiceParserInterface
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException("Invoice parser file does not exist");
        }

        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        
        $parser = match($fileExtension) {
            'json' => new InvoiceJsonParser(),
            'csv' => new InvoiceCsvParser(),
            default => null 
        };

        return $parser;
    }
}