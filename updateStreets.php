<?php

ini_set('max_execution_time', 600);

$pdo = new PDO("mysql:host=localhost;dbname=network_controll;charset=utf8", "root", "");

$sql = 'SELECT id, name FROM streets';

$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ( $result as $name ) {
    $a = explode(' ', $name['name']);
    if ( $a[0] !== 'ул.' ) {
        $newName = 'ул. ' . $name['name'];
        $id = $name['id'];

        $sql = 'UPDATE streets SET name = ? WHERE id = ?';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$newName, $id]);
    }
}