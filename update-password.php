<?php
require_once 'includes/DB_class.php';

$password_new = $_POST['password_new'];
$token = $_GET['token'];

$db=DataBase::getPDO();


$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = :token ");
$stmt->execute(array(':token' => $token));
$result = $stmt->rowCount();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user) {
    $stmt = $db->prepare("UPDATE users SET password = :password, reset_token = NULL WHERE id = :id");
    $stmt->execute(array(':password' => password_hash($password_new, PASSWORD_DEFAULT), ':id' => $user['id']));
   
    header("Location: login.php");
    exit();
} else {
    $error = "Invalid or expired token.";
}
