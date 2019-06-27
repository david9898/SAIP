<?php


namespace App\Repository;


use App\DTO\BillDTO;

interface BillRepositoryInterface
{
    public function addNewBill(BillDTO $billDTO): bool;

    public function getBillsOnClient($id): \Generator;

    public function removeBill($id): bool;
}