<?php
// include the database connection and helper functions
// require_once('../includes/config.php');
include('../includes/config.php');
require_once('../includes/functions.php');
 $pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);


// Check if user is logged in as admin
if (!is_admin()) {
    redirect('index.php');
    // echo "not admin";
}
include 'nav-admin.php';

echo "<h1>welcome ,<span class='text-success'>Admin</span></h1>";
// // Get total number of products
$query1 = "SELECT COUNT(*) as total_products FROM products";
$stmt1 = $pdo->query($query1);
$row1 = $stmt1->fetch();
$total_products = $row1['total_products'];

// // Get total number of categories
$query2 = "SELECT COUNT(*) as total_categories FROM categories";
$stmt2 = $pdo->query($query2);
$row2 = $stmt2->fetch();
$total_categories = $row2['total_categories'];

// Get total number of users
$query3 = "SELECT COUNT(*) as total_users FROM users";
$stmt3 = $pdo->query($query3);
$row3 = $stmt3->fetch();
$total_users = $row3['total_users'];

// // Get total number of orders
$query4 = "SELECT COUNT(*) as total_orders FROM orders";
$stmt4 = $pdo->query($query4);
$row4 = $stmt4->fetch();
$total_orders = $row4['total_orders'];

// Display admin dashboard
// include_once '../includes/header.php';
?>
<div class="container">
    <h1 class="text-center">Admin Dashboard</h1>
    <hr>
    <div class="row">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Total Products</h3>
                </div>
                <div class="card-body">
                <h2><?php echo $total_products; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Total Categories</h3>
                </div>
                <div class="card-body">
                <h2><?php echo $total_categories; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Total Users</h3>
                </div>
                <div class="card-body">
                    <h2><?php echo $total_users; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Total Orders</h3>
                </div>
                <div class="card-body">
                <h2><?php echo $total_orders; ?></h2>
                </div>
            </div>

            <div class="d-flex mx-auto my-5">
               <a href='add_new_product.php' class="btn btn-success"> add product</a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <h1>Welcome to the Cafeteria</h1>

    <div class="row">
        <?php
        $products = get_products();
        var_dump(count($products));
        if(count($products)==0)
        {
            echo "there are no products";
        }
        else{
  foreach ($products as $product) {
    ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../images/<?php echo $product['image_url']; ?>" class="card-img-top w-50" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                    </div>
                    <div class="card-footer">
                        <form action="add-to-cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="mb-3">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                            </div>
                            <div class="mb-3">
                                <label for="notes">Notes:</label>
                                <textarea name="notes" id="notes" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php
}
        }
?>
    </div>

<?php include_once '../includes/footer.php'; ?>