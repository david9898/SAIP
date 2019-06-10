<?php
/**
 * Created by PhpStorm.
 * User: Toshiba
 * Date: 13.12.2018 г.
 * Time: 14:17 ч.
 */

namespace Core\Database;

class FetchStatement implements FetchStatementInterface
{

    private $stmt;

    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function fetchObject($className): \Generator
    {
        while ($row = $this->stmt->fetchObject($className)) {
            yield $row;
        }
    }

    public function fetchAssoc(): \Generator
    {
        while ($row = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

}