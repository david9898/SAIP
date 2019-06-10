<?php

ini_set('max_execution_time', 300);
$townId = 6;

$str = file_get_contents('map/Dragoman/Dragoman.geojson');

$enc = json_decode($str, true);

$pdo = new \PDO('mysql:host=localhost;dbname=network_controll;charset=utf8', 'root', null);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$arr = [];

foreach ( $enc['features'] as $key => $value ) {
    if ( $value['properties']['name'] !== null ) {
        if ( in_array($value['properties']['name'], $arr) === false ) {
            $sql = 'INSERT INTO streets(name) VALUES (?)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$value['properties']['name']]);
            $arr[] = $value['properties']['name'];
            $lastId = $pdo->lastInsertId();
            $sql = 'INSERT INTO relations_towns_streets(town, street) VALUES (?, ?)';
            $astmt = $pdo->prepare($sql);
            $astmt->execute([$townId, $lastId]);
        }
    }
}
