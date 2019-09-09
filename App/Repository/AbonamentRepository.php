<?php


namespace App\Repository;


use App\DTO\AbonamentDTO;
use Core\Database\PrepareStatementInterface;

class AbonamentRepository implements AbonamentRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getAbonaments(): \Generator
    {
        $sql = 'SELECT id, name FROM abonaments';

        return $this->db->prepare($sql)
                ->execute()
                ->fetchObject(AbonamentDTO::class);
    }

    public function checkIfAbonamentExist(string $name): ?AbonamentDTO
    {
        $sql = 'SELECT id FROM abonaments WHERE name = :name';

        return $this->db->prepare($sql)
                        ->bindParam('name', $name, \PDO::PARAM_STR)
                        ->execute()
                        ->fetchObject(AbonamentDTO::class)
                        ->current();
    }

    public function addAbonament(AbonamentDTO $abonamentDTO): bool
    {
        $sql = 'INSERT INTO abonaments (name, price, description)
                VALUES (:name, :price, :description)';

        $this->db->prepare($sql)
                ->bindParam('name', $abonamentDTO->getName(), \PDO::PARAM_STR)
                ->bindParam('price', $abonamentDTO->getPrice(), \PDO::PARAM_STR)
                ->bindParam('description', $abonamentDTO->getDescription(), \PDO::PARAM_STR)
                ->execute();

        return true;
    }

    public function getAbonamentPrice($id): AbonamentDTO
    {
        $sql = 'SELECT price FROM abonaments WHERE id = :id';

        return $this->db->prepare($sql)
                        ->bindParam('id', $id, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(AbonamentDTO::class)
                        ->current();
    }

}