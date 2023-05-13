<?php

require_once('../includes/config.php');
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Check if the product ID is provided
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the associated order items first
    $delete_order_items_query = "DELETE FROM order_items WHERE product_id = :product_id";
    $delete_order_items_stmt = $pdo->prepare($delete_order_items_query);
    $delete_order_items_stmt->bindValue(':product_id', $product_id);
    $delete_order_items_stmt->execute();

    // Delete the product
    $delete_product_query = "DELETE FROM products WHERE id = :product_id";
    $delete_product_stmt = $pdo->prepare($delete_product_query);
    $delete_product_stmt->bindValue(':product_id', $product_id);

    if ($delete_product_stmt->execute()) {
        // Redirect to the products page
        header('Location: products.php');
        exit();
    } else {
        $error_message = 'An error occurred while deleting the product. Please try again later.';
    }
} else {
    $error_message = 'Product ID not provided.';
}

// Display error message if exists
if (isset($error_message)) {
    echo $error_message;
}
