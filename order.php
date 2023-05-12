<?php
// include 'includes/DB_class.php';
require_once('includes/functions.php');

if (!is_logged_in()) {
    redirect('login.php');
}

// if the user submitted an order
if (isset($_POST['submit_order'])) {
    // get the user's selected items and order details

    echo "yes submit_order";
    // $items = $_POST['items'];
    // $notes = $_POST['notes'];
    // $room_no = $_POST['room_no'];
    // $total_price = calculate_total_price($items);

    // // insert the order into the database
    // $db = new DB();
    // $db->insertOrder($_SESSION['user_id'], $room_no, $total_price, 'processing');

    // // get the ID of the newly inserted order
    // $order_id = $db->getLastInsertedOrderId();

    // // insert the selected items into the order_items table
    // foreach ($items as $item_id => $count) {
    //     if ($count > 0) {
    //         $db->insertOrderItem($order_id, $item_id, $count, $notes[$item_id]);
    //     }
    // }

    // // redirect the user to their orders page
    // redirect('orders.php');
}

include 'nav-user.php';
?>
