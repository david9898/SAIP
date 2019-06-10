<?php
/**
 * Created by PhpStorm.
 * User: Toshiba
 * Date: 13.12.2018 г.
 * Time: 13:36 ч.
 */

namespace Core\Database;


interface ExecuteStatementInterface
{
    public function execute(): FetchStatementInterface;

    public function bindParam(string $name, $value, $pdoParam): ExecuteStatementInterface;
}