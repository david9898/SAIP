<?php


namespace App\Repository;


use App\DTO\StaffDTO;

interface StaffRepositoryInterface
{
    public function getCustomer(string $username): ?StaffDTO;

    public function addCustomer(StaffDTO $customer): bool;
}