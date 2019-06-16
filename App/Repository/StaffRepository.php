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

}