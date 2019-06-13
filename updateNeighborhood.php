<?php

ini_set('max_execution_time', 600);

$pdo = new PDO("mysql:host=localhost;dbname=network_controll;charset=utf8", "root", "");

$sql = 'SELECT id, name FROM neighborhood';

$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $item) {
    $newName = 'кв. ' . $item['name'];
    $id = $item['id'];

    $sql = 'UPDATE neighborhood SET name = ? WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newName, $id]);
}