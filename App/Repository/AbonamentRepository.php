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

    public function getAbonaments()
    {
        $sql = 'SELECT id, name FROM abonaments';

        return $this->db->prepare($sql)
                ->execute()
                ->fetchObject(AbonamentDTO::class);
    }

}