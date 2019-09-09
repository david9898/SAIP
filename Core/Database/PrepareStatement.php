<?php
/**
 * Created by PhpStorm.
 * User: Toshiba
 * Date: 13.12.2018 г.
 * Time: 14:07 ч.
 */

namespace Core\Database;

class PrepareStatement implements PrepareStatementInterface
{

    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function prepare(string $sql): ExecuteStatementInterface
    {
        $stmt = $this->db->prepare($sql);

        return new ExecuteStatement($stmt);
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->db->commit();
    }

    public function rollBack(): bool
    {
        return $this->db->rollBack();
    }


}