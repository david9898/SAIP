<?php


namespace App\Repository;


use App\DTO\StaffDTO;

interface StaffRepositoryInterface
{
    public function getCustomer(string $username): ?StaffDTO;

    public function addCustomer(StaffDTO $customer): bool;

    public function getCustomerRoles($id): \Generator;

    public function addRole($staffId, $roleId): bool;

    public function getAllCustomers(): \Generator;

    public function getOneCustomer($customerId): StaffDTO;

    public function updateCustomer(StaffDTO $customer): bool;

    public function deleteRoles($customerId): bool;

    public function getStaffPass($customerId): StaffDTO;

    public function disableStaff($customerId): bool;
}