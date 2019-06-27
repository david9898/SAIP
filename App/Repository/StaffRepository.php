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
        $sql = 'SELECT staff.id, role_name as role, username, password 
                FROM staff JOIN roles ON staff.role = roles.id 
                WHERE staff.username = ?';

        return $this->db->prepare($sql)
                        ->execute([$username])
                        ->fetchObject(StaffDTO::class)
                        ->current();
    }

    public function addCustomer(StaffDTO $customer): bool
    {
        $sql = 'INSERT INTO staff (first_name, last_name, username, password, phone, role)
                VALUES (?, ?, ?, ?, ?, ?)';

        $this->db->prepare($sql)
                ->execute([$customer->getFirstName(), $customer->getLastName(), $customer->getUsername(),
                            $customer->getPassword(), $customer->getPhone(), $customer->getRole()]);

        return true;
    }
}