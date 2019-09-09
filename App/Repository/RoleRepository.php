<?php


namespace App\Repository;


use App\DTO\RoleDTO;
use Core\Database\PrepareStatementInterface;

class RoleRepository implements RoleRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getAllRoles(): \Generator
    {
        $sql = 'SELECT id, role_name as roleName FROM roles';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(RoleDTO::class);
    }

}