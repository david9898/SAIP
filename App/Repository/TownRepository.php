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

    public function getTowns(): \Generator
    {
        $sql = 'SELECT id, name FROM towns';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(TownDTO::class);
    }

    public function checkForStreetInTown($townId, $streetId): bool
    {
        $sql = 'SELECT town, street FROM relations_towns_streets WHERE town = ? AND street = ?';

        $query = $this->db->prepare($sql)
                        ->execute([$townId, $streetId])
                        ->fetchAssoc();

        if ( $query !== null ) {
            return true;
        }

        return false;
    }

}