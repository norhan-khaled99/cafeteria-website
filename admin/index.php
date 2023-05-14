<?php
include('../includes/DB_class.php');
require('../includes/functions.php');
$pdo = DataBase::getPDO();

// Check if user is logged in as admin
if (!is_admin()) {
    redirect('index.php');
}

include 'nav-admin.php';


$orders=get_all_orders();
$users=get_all_users();





// Get total number of products
$query1 = "SELECT COUNT(*) as total_products FROM products";
$stmt1 = $pdo->query($query1);
$row1 = $stmt1->fetch();
$total_products = $row1['total_products'];

// Get total number of categories
$query2 = "SELECT COUNT(*) as total_categories FROM categories";
$stmt2 = $pdo->query($query2);
$row2 = $stmt2->fetch();
$total_categories = $row2['total_categories'];

// Get total number of users
$query3 = "SELECT COUNT(*) as total_users FROM users";
$stmt3 = $pdo->query($query3);
$row3 = $stmt3->fetch();
$total_users = $row3['total_users'];

// Get total number of orders
$query4 = "SELECT COUNT(*) as total_orders FROM orders";
$stmt4 = $pdo->query($query4);
$row4 = $stmt4->fetch();
$total_orders = $row4['total_orders'];

?>

<div class="container">
    <h1 class="text-center my-3">Admin Dashboard</h1>
    <hr>
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-center bg-primary text-white">
                <div class="card-header">
                    <h3>Total Products</h3>
                </div>
                <div class="card-body">
                    <h2><?php echo $total_products; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center bg-success text-white">
                <div class="card-header">
                    <h3>Total Categories</h3>
                </div>
                <div class="card-body">
                    <h2><?php echo $total_categories; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center bg-info text-white">
                <div class="card-header">
                    <h3>Total Users</h3>
                </div>
                <div class="card-body">
                    <h2><?php echo $total_users; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center bg-warning text-dark">
                <div class="card-header">
                    <h3>Total Orders</h3>
                </div>
                <div class="card-body">
                    <h2><?php echo $total_orders; ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <table class="table">
          <thead>
            <th>order date</th>
            <th>user name</th>
            <th>room</th>
            <th>ext</th>
            <th>action</th>
          </thead>
          <tbody>
            <?php foreach($orders as $order){?>
            <tr>
                <td><?php echo $order['order_date'];?> </td>

                <?php foreach($users as $user){
                     if($user['id']==$order['user_id']){
                     ?>
                <td><?php echo $user['name'];?> </td>
                <?php }} ?>

                <td><?php echo $order['room_no'];?> </td>

                <?php foreach($users as $user){
                     if($user['id']==$order['user_id']){
                     ?>
                <td><?php $user['ext'] ?></td>
                <?php }} ?>

                <td><?php echo $order['order_status'];?> </td>
            </tr>
            <?php } ?>
          </tbody>
</table>
    </div>
</div>
<div class="bg-secondary text-center py-2 fixed-bottom">
        <p class="mb-0">All rights reserved &copy; 2023</p>
    </div>
