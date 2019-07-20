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
                WHERE staff.username = ?';

        return $this->db->prepare($sql)
                        ->execute([$username])
                        ->fetchObject(StaffDTO::class)
                        ->current();
    }

    public function addCustomer(StaffDTO $customer): bool
    {
        $sql = 'INSERT INTO staff (first_name, last_name, username, password, phone)
                VALUES (?, ?, ?, ?, ?)';

        $this->db->prepare($sql)
                ->execute([$customer->getFirstName(), $customer->getLastName(), $customer->getUsername(),
                            $customer->getPassword(), $customer->getPhone()]);

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