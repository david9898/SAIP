<?php


namespace App\Repository;


use App\DTO\InvoiceDTO;

interface InvoiceRepositoryInterface
{

    public function addInvoice(InvoiceDTO $invoice): bool ;

    public function getInvoicesOnClient($clientId): \Generator;

    public function getUnpaidInvoicesOnClient($clientId): ?\Generator;

    public function payInvoice($invoiceId, $operator, $time): bool;

    public function payAllInvoices($clientId, $operator, $time): bool;

    public function getLastInvoiceOnClient($clientId): InvoiceDTO;
}