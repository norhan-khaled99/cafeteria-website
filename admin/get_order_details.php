<?php
// get_order_details.php

// Include necessary files and functions
include '../includes/DB_class.php';
require_once('../includes/functions.php');

// Create a new PDO instance
$pdo = DataBase::getPDO();

// Check if the check ID is provided
if (isset($_GET['check_id'])) {
    $checkId = $_GET['check_id'];
    
    // Retrieve the order details based on the check ID
    $orderDetails = get_order_details($pdo, $checkId);
    
    // Return the order details as JSON
    header('Content-Type: application/json');
    echo json_encode($orderDetails);
}
?>
