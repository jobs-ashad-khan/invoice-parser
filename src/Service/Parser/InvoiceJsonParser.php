<?php

declare(strict_types=1);

namespace App\Service\Parser;

class InvoiceJsonParser implements InvoiceParserInterface
{
    public function parse(string $path): array
    {
        $fileContent = file_get_contents($path);
        
        $invoices = json_decode($fileContent, true);
        if (!is_array($invoices)) {
            throw new \Exception("Le fichier JSON est invalide.");
        }

        return $invoices;
    }
}