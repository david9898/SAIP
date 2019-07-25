<?php


namespace App\Repository;


use App\DTO\StaffDTO;
use Core\Database\PrepareStatementInterface;

class StaffRepository  implements StaffRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getCustomer(string $username): ?StaffDTO
    {
        $sql = 'SELECT staff.id, username, password 
                FROM staff
                WHERE staff.username = :username';

        return $this->db->prepare($sql)
                        ->bindParam('username', $username, \PDO::PARAM_STR)
                        ->execute()
                        ->fetchObject(StaffDTO::class)
                        ->current();
    }

    public function addCustomer(StaffDTO $customer): bool
    {
        $sql = 'INSERT INTO staff (first_name, last_name, username, password, phone)
                VALUES (:firstName, :lastName, :username, :password, :phone)';

        $this->db->prepare($sql)
                ->bindParam('firstName', $customer->getFirstName(), \PDO::PARAM_STR)
                ->bindParam('lastName', $customer->getLastName(), \PDO::PARAM_STR)
                ->bindParam('username', $customer->getUsername(), \PDO::PARAM_STR)
                ->bindParam('password', $customer->getPassword(), \PDO::PARAM_STR)
                ->bindParam('phone', $customer->getPhone(), \PDO::PARAM_STR)
                ->execute();

        return true;
    }

    public function getCustomerRoles($id): \Generator
    {
        $sql = 'SELECT role_name as role FROM roles 
                JOIN relations_staff_roles r ON r.role_id = roles.id
                WHERE r.staff_id = :id';

        return $this->db->prepare($sql)
                        ->bindParam('id', $id, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc();
    }

    public function addRole($staffId, $roleId): bool
    {
        $sql = 'INSERT INTO relations_staff_roles (staff_id, role_id)
                VALUES (:staffId, :roleId)';

        $this->db->prepare($sql)
                ->bindParam('staffId', $staffId, \PDO::PARAM_INT)
                ->bindParam('roleId', $roleId, \PDO::PARAM_INT)
                ->execute();

        return true;
    }

}