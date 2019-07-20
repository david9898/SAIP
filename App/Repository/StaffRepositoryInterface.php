<?php


namespace App\Repository;


use App\DTO\StaffDTO;

interface StaffRepositoryInterface
{
    public function getCustomer(string $username): ?StaffDTO;

    public function addCustomer(StaffDTO $customer): bool;

    public function getCustomerRoles($id): \Generator;

    public function addRole($staffId, $roleId): bool;

}