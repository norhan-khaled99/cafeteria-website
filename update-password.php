<?php

$password = $_POST['password'];
$token = $_GET['token'];
$host = 'localhost';
$dbname = 'cafeteriaWebsiteDB';
$username = 'root';
$password = 'pass';
$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = :token ");
$stmt->execute(array(':token' => $token));
$result = $stmt->rowCount();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($user);
if ($user) {
    $stmt = $db->prepare("UPDATE users SET password = :password, reset_token = NULL WHERE id = :id");
    $stmt->execute(array(':password' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']));
    header("Location: login.php");
    exit();
} else {
    $error = "Invalid or expired token.";
}
