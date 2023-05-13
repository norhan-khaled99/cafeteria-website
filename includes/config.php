<?php

$host = 'localhost';
$username = 'root';
$password = 'pass';
$dbname = 'cafeteriaWebsiteDB';
// $host = 'localhost';
// $dbname = 'cafeteria_db';
// $username = 'phpuser';
// $password = 'Iti123456';
try {
    // $pdo = new PDO("mysql:host=$host;", $username, $password);
    $pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $pdo->exec($sql);

    // echo "Database created successfully";
} catch(PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}
