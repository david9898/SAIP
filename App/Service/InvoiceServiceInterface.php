<?php


namespace App\Service;


use App\Repository\ClientRepositoryInterface;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;

interface InvoiceServiceInterface
{

    public function addInvoicesToAllClients(InvoiceRepositoryInterface $invoiceRepository, ClientRepositoryInterface $clientRepository): bool ;

    public function generateInvoices($unPaidInvoices, $numInvoices, $clientId, InvoiceRepositoryInterface $invoiceRepository,
                                     $clientAbonamentPrice): bool;

    public function payInvoices(InvoiceRepositoryInterface $invoiceRepository, PaymentRepositoryInterface $paymentRepository,
                                ClientRepositoryInterface $clientRepository, $postArr): array;
}