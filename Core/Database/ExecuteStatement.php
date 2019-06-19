<?php
/**
 * Created by PhpStorm.
 * User: Toshiba
 * Date: 13.12.2018 г.
 * Time: 14:10 ч.
 */

namespace Core\Database;


class ExecuteStatement implements ExecuteStatementInterface
{

    private $stmt;

    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function bindParam(string $name, $value, $pdoParam, $count = null): ExecuteStatementInterface
    {
        $this->stmt->bindParam($name, $value, $pdoParam);

        return new ExecuteStatement($this->stmt);
    }

    public function execute(array $arr = null): FetchStatementInterface
    {
        $this->stmt->execute($arr);

        return new FetchStatement($this->stmt);
    }

}