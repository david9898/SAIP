<?php


namespace App\Repository;

use App\DTO\TownDTO;
use Core\Database\PrepareStatementInterface;

class TownRepository implements TownRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getTowns()
    {
        $sql = 'SELECT id, name FROM towns';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(TownDTO::class);
    }
}