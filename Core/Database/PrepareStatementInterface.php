<?php

namespace Core\Database;

interface PrepareStatementInterface
{
    public function prepare(string $sql): ExecuteStatementInterface;

    public function lastInsertId();

    public function beginTransaction(): bool;

    public function commit(): bool;

    public function rollBack(): bool;
}