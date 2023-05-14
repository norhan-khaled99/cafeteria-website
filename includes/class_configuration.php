<?php
class configuratins{
 public static  $host = 'localhost';
 public static $dbname = 'cafeteria_db';
 public static $username = 'phpuser';
 public static $password = 'Iti123456';

 function __construct(){
 try {
    $pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $pdo->exec($sql);

} catch(PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}

 }




}