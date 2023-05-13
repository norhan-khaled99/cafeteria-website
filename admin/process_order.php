<?php
include '../includes/DB_class.php';
require_once('../includes/functions.php');

// Create a new PDO instance
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Create the necessary tables
create_tables($pdo);

// Check if the user is logged in and is an admin
check_session();

// Get all users from the database
$users = get_all_users();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the JSON data is received
    $jsonData = file_get_contents('php://input');
    if ($jsonData) {
        // Decode the JSON data
        $data = json_decode($jsonData, true);

        // Extract the orders and user ID from the data
        $orders = $data['orders'];
        $userId = $data['userId'];

        // Process the orders
        $success = processOrder($userId, $orders);

        // Prepare the response data
        $response = array('success' => $success);

        // Send the JSON response
        header('Content-Type: application/json');
        echo json_encode($response);

        // Redirect to admin-order.php after successful saving
        if ($success) {
            header('Location: admin-order.php');
            exit();
        }
    } else {
        // JSON data not received
        $response = array('success' => false);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

// Function to check if a user is valid
function isValidUser($userId)
{
    global $users;

    foreach ($users as $user) {
        if ($user['id'] == $userId) {
            return true;
        }
    }

    return false;
}

// Function to process the order
function processOrder($userId, $selectedItems)
{
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert the order into the orders table
        $insertOrderStmt = $pdo->prepare('INSERT INTO orders (user_id) VALUES (:user_id)');
        $insertOrderStmt->bindParam(':user_id', $userId);
        $insertOrderStmt->execute();

        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert the order items into the order_items table
        $insertOrderItemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity) VALUES (:order_id, :product_id, :quantity)');
        foreach ($selectedItems as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];
            $insertOrderItemStmt->bindParam(':order_id', $orderId);
            $insertOrderItemStmt->bindParam(':product_id', $productId);
            $insertOrderItemStmt->bindParam(':quantity', $quantity);
            $insertOrderItemStmt->execute();
        }

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $pdo->rollback();
        return false;
    }
}
?>
