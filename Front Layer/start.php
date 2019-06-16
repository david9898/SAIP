<?php

$dbParams = parse_ini_file('Configurations/db.ini');

$pdo = new \PDO($dbParams['dsn'], $dbParams['user'], $dbParams['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$db = new \Core\Database\PrepareStatement($pdo);