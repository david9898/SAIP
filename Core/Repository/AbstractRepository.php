<?php


namespace Core\Repository;


use Core\Database\PrepareStatementInterface;

abstract class AbstractRepository
{
    protected $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollBack() {
        return $this->db->rollBack();
    }
}