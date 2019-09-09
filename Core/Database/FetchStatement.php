<?php

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

    public function fetchGroupObject($className, array $rows): \Generator
    {
        $generator = $this->fetchObject($className);

        foreach ($generator as $item) {
            foreach ($rows as $row) {
                $getFunc = 'get' . ucfirst($row);
                $setFunc = 'set' . ucfirst($row);
                $data    = $item->$getFunc();
                $newData = explode(',', $data);
                $item->$setFunc($newData);
            }

            yield $item;
        }
    }

}