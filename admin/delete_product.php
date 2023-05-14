<?php
require('../includes/functions.php');
$pdo = DataBase::getPDO();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $delete_order_items_query = "DELETE FROM order_items WHERE product_id = :product_id";
    $delete_order_items_stmt = $pdo->prepare($delete_order_items_query);
    $delete_order_items_stmt->bindValue(':product_id', $product_id);
    $delete_order_items_stmt->execute();

    $delete_product_query = "DELETE FROM products WHERE id = :product_id";
    $delete_product_stmt = $pdo->prepare($delete_product_query);
    $delete_product_stmt->bindValue(':product_id', $product_id);

    if ($delete_product_stmt->execute()) {
        header('Location: products.php');
        exit();
    } else {
        $error_message = 'An error occurred while deleting the product. Please try again later.';
    }
} else {
    $error_message = 'Product ID not provided.';
}

if (isset($error_message)) {
    echo $error_message;
}
