<?php


namespace App\Repository;


use App\DTO\StaffDTO;
use Core\Repository\AbstractRepository;

class StaffRepository extends AbstractRepository implements StaffRepositoryInterface
{

    public function getCustomer(string $username): ?StaffDTO
    {
        $sql = 'SELECT staff.id, username, password 
                FROM staff
                WHERE staff.username = :username AND disabled = 0';

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

    public function getAllCustomers(): \Generator
    {
        $sql = 'SELECT staff.id, CONCAT(first_name, " ", last_name) as firstName, phone, username, GROUP_CONCAT(roles.role_name) as roles
                FROM staff 
                LEFT JOIN relations_staff_roles ON staff.id = relations_staff_roles.staff_id
                LEFT JOIN roles ON roles.id = relations_staff_roles.role_id
                WHERE staff.disabled = 0
                GROUP BY staff.id';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchGroupObject(StaffDTO::class, ['roles']);
    }

    public function getOneCustomer($customerId): StaffDTO
    {
        $sql = 'SELECT staff.id, first_name as firstName, last_name as lastName, phone, username, GROUP_CONCAT(roles.role_name) as roles
                FROM staff 
                LEFT JOIN relations_staff_roles ON staff.id = relations_staff_roles.staff_id
                LEFT JOIN roles ON roles.id = relations_staff_roles.role_id
                WHERE staff.id = :customerId
                GROUP BY staff.id';

        return $this->db->prepare($sql)
                        ->bindParam('customerId', $customerId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchGroupObject(StaffDTO::class, ['roles'])
                        ->current();
    }

    public function updateCustomer(StaffDTO $customer): bool
    {
        $sql = 'UPDATE staff 
                SET first_name = :firstName, last_name = :lastName, username = :username, phone = :phone, password = :password
                WHERE disabled = 0 AND id = :id';

        $this->db->prepare($sql)
                    ->bindParam('id', $customer->getId(), \PDO::PARAM_INT)
                    ->bindParam('firstName', $customer->getFirstName(), \PDO::PARAM_STR)
                    ->bindParam('lastName', $customer->getLastName(), \PDO::PARAM_STR)
                    ->bindParam('username', $customer->getUsername(), \PDO::PARAM_STR)
                    ->bindParam('phone', $customer->getPhone(), \PDO::PARAM_STR)
                    ->bindParam('password', $customer->getPassword(), \PDO::PARAM_STR)
                    ->execute();

        return true;
    }

    public function deleteRoles($customerId): bool
    {
        $sql = 'DELETE FROM relations_staff_roles WHERE staff_id = :staffId';

        $this->db->prepare($sql)
                ->bindParam('staffId', $customerId, \PDO::PARAM_INT)
                ->execute();

        return true;
    }

    public function getStaffPass($customerId): StaffDTO
    {
        $sql = 'SELECT password FROM staff WHERE id = :customerId';

        return $this->db->prepare($sql)
                        ->bindParam('customerId', $customerId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(StaffDTO::class)
                        ->current();
    }

    public function disableStaff($customerId): bool
    {
        $sql = 'UPDATE staff SET disabled = 1 WHERE id = :customerId';

        $this->db->prepare($sql)
                ->bindParam('customerId', $customerId, \PDO::PARAM_INT)
                ->execute();

        return true;
    }

}