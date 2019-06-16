<?php


namespace App\Repository;


use App\DTO\StreetDTO;
use Core\Database\PrepareStatementInterface;
use phpDocumentor\Reflection\Types\This;

class StreetRepository implements StreetRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getTownStreets($id): \Generator
    {
        $sql = 'SELECT name FROM relations_towns_streets 
            JOIN streets ON relations_towns_streets.street = streets.id 
            WHERE relations_towns_streets.town = :id';

        return $this->db->prepare($sql)
            ->bindParam('id', $id, \PDO::PARAM_INT)
            ->execute()
            ->fetchAssoc();
    }

    public function getStreetByName($name): ?StreetDTO
    {
        $sql = 'SELECT id FROM streets WHERE name = ?';

        return $this->db->prepare($sql)
                        ->execute([$name])
                        ->fetchObject(StreetDTO::class)
                        ->current();

    }


}