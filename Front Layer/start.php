<?php

$dbParams = parse_ini_file('Configurations/db.ini');

$pdo = new \PDO($dbParams['dsn'], $dbParams['user'], $dbParams['password']);

$db = new \Core\Database\PrepareStatement($pdo);