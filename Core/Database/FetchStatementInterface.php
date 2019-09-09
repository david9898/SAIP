<?php
/**
 * Created by PhpStorm.
 * User: Toshiba
 * Date: 13.12.2018 г.
 * Time: 14:06 ч.
 */

namespace Core\Database;

interface FetchStatementInterface
{
    public function fetchObject($className): \Generator;

    public function fetchAssoc(): \Generator;

    public function fetchGroupObject($className, array $rows): \Generator;
}