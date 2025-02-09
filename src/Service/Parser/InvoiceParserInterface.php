<?php

declare(strict_types=1);

namespace App\Service\Parser;

interface InvoiceParserInterface
{
    public function parse(string $path): array;
}