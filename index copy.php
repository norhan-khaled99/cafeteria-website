<?php
include 'includes/DB_class.php';
require_once('includes/functions.php');

// Create a new PDO instance
$pdo = DataBase::connect();

// Create the necessary tables
create_tables($pdo);

if (!is_logged_in()) {
    redirect('login.php');
}

include 'nav-user.php';
?>

<div class="container">
    <h1>Welcome to the Cafeteria</h1>

    <div class="row">
        <?php
        $products = get_products();
if(count($products)==0) {
    echo "There are no products.";
} else {
    ?>
    
        <div class="row d-flex">
            <div class="col-9">
                <div class="container">
                    <div class="row d-flex">
                        <div class="col-6">
                            <h1 class='text-primary'>Order Form</h1>
                            <div class="container border border-primary w-50 p-3">
                                <form action="order.php" method="post">
                                    <div class="form-group">
                                        <?php foreach ($products as $product) { ?>
                                        <label for=""><?php echo $product['name']; ?></label>
                                        <div class="mb-3">
                                            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                                        </div>
                                        <?php } ?>
                                        <label for="room_no">Room:</label>
                                        <select class="form-control w-50" id="room_no" name="room_no" required>
                                            <option selected></option>
                                            <option value='combobox'>Combobox</option>
                                        </select>
                                        <div class="mb-3">
                                            <label for="notes">Notes:</label>
                                            <textarea name="notes" id="notes" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" name="submit_order" class="btn btn-primary">Confirm Order</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-6"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
    <?php foreach ($products as $product) { ?>
    <div class="card">
        <img src="images/<?php echo $product['image_url']; ?>" class="card-img-top w-50" alt="Product Image">
        <div class="card-body">
            <h5 class="card-title"><?php echo $product['name']; ?></h5>
        </div>
    </div>
    <?php } ?>
</div>

        </div>
        <?php } ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
