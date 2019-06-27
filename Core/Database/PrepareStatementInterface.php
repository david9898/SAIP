<?php

namespace Core\Database;

interface PrepareStatementInterface
{
    public function prepare(string $sql): ExecuteStatementInterface;

}