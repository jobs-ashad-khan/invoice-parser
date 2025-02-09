<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Invoice;
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

        if (str_contains($fp, 'json')) {
            $fileContent = file_get_contents($fp);
            $invoicesData = json_decode($fileContent, true);

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
        } elseif (str_contains($fp, 'csv')) {   //Pour les json
            if (($handle = fopen($fp, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
                    $invoice = $invoiceRepository->findOneByName($data[2]);
                    if (!$invoice) {
                        $invoice = new Invoice();
                        $invoice->setName($data[2]);
                        $invoice->setCurrency($data[1]);
                    }
    
                    $invoice->setAmount(floatval($data[0]));
                    $invoice->setCurrency($data[1]);
                    $this->em->persist($invoice);
                    $this->em->flush();
                }
                fclose($handle);
            }    
        }
    }
}
