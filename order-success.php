<?php
include 'includes/DB_class.php';
require_once('includes/functions.php');

// Create a new PDO instance
$pdo = DataBase::connect();

include 'nav-user.php';

// Check if the user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Retrieve the order details
$orderID = $_GET['order_id']; // Assuming the order ID is passed through the URL
$order = get_order_by_id($orderID);

// Display the success message and order details
?>

<div class="container">
    <h1>Order Success</h1>
    <p>Thank you for your order! Your order has been successfully placed.</p>
    
    <h2>Order Details</h2>
    <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
    <p><strong>Product:</strong> <?php echo $order['product_name']; ?></p>
    <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
    <p><strong>Room No:</strong> <?php echo $order['room_no']; ?></p>
    <p><strong>Notes:</strong> <?php echo $order['notes']; ?></p>
    <p><strong>Total Amount:</strong> <?php echo $order['total_amount']; ?></p>
    
    <!-- You can add more details or customize the message as per your requirements -->
</div>

<!-- Rest of the HTML code or any additional styling/scripts -->
