<?php


namespace App\Repository;

use Core\Database\PrepareStatementInterface;

class NeighborhoodRepository implements NeighborhoodRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getTownNeighborhoods($townId): \Generator
    {
        $sql = 'SELECT name FROM relations_towns_neighborhoods 
                JOIN neighborhoods ON relations_towns_neighborhoods.neighborhood_id = neighborhoods.id 
                WHERE relations_towns_neighborhoods.town_id = :townId';

        return $this->db->prepare($sql)
                        ->bindParam('townId', $townId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc();
    }

    public function checkTownHaveNeighborhood($townId): bool
    {
        $sql = 'SELECT neighborhood_id FROM relations_towns_neighborhoods WHERE town_id = :townId';

        $query = $this->db->prepare($sql)
                        ->bindParam('townId', $townId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc()
                        ->current();

        if ( $query !== null ) {
            return true;
        }

        return false;
    }

    public function getNeighborhoodId($name): ?int
    {
        $sql = 'SELECT id FROM neighborhoods WHERE name = ?';

        $query = $this->db->prepare($sql)
                        ->execute([$name])
                        ->fetchAssoc()
                        ->current();

        return $query['id'];
    }

    public function checkForValidNeighborhood($townId, $neighborhoodId): bool
    {
        $sql = 'SELECT town_id FROM relations_towns_neighborhoods WHERE town_id = ? AND neighborhood_id = ?';

        $query = $this->db->prepare($sql)
                        ->execute([$townId, $neighborhoodId])
                        ->fetchAssoc()
                        ->current();

        if ( $query !== null ) {
            return true;
        }

        return false;
    }
}