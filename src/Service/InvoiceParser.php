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


            $d = array_map(function($r) {
                return str_getcsv($r, "\t");
            }, file($fp));
            $c = 0;
                while(true){
                if(isset($d[$c])){
                    $this->em->getConnection()->executeStatement(
                        "UPDATE invoice SET amount = {$d[$c][0]} WHERE name = '{$d[$c][2]}'"
                    );
                    $c++;
                }else{
                    break;
                }
                }
        }
    }
}
