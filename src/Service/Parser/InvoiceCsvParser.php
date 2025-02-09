<?php

declare(strict_types=1);

namespace App\Service\Parser;

class InvoiceCsvParser implements InvoiceParserInterface
{
    public function parse(string $path): array
    {
        $invoices = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
                $invoices[] = [
                    'montant' => floatval($data[0]),
                    'devise' => $data[1],
                    'nom' => $data[2],
                    'date' => $data[3]
                ];
            }
            fclose($handle);
        } 
    
        return $invoices;
    }
}