<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Invoice;
use App\Service\Parser\InvoiceParserFactory;
use Doctrine\ORM\EntityManagerInterface;


class InvoiceParser
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function parse(string $fp): void
    {
        $invoiceRepository = $this->em->getRepository(Invoice::class);
        $invoiceParser = InvoiceParserFactory::create($fp);

        $invoicesData = $invoiceParser->parse($fp);

        foreach($invoicesData as $invoiceData) {
            $invoice = $invoiceRepository->findOneByName($invoiceData['nom']);
            if (!$invoice) {
                $invoice = new Invoice();
                $invoice->setName($invoiceData['nom']);
                $invoice->setCurrency($invoiceData['devise']);
            }

            $invoice->setAmount(floatval($invoiceData['montant']));
            $invoice->setCurrency($invoiceData['devise']);
            $this->em->persist($invoice);
            $this->em->flush();
        }
    }
}
