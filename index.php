
<?php
include 'includes/DB_class.php';
require_once('includes/functions.php');

// Create a new PDO instance
$pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
// Create the necessary tables
// create_tables($pdo);

if (!is_logged_in()) {
    redirect('login.php');
}

include 'nav-user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is submitted
    if (isset($_POST['submit_order']) && isset($_POST['selected_product'])) {
        // Retrieve the form values
        $selectedProduct = $_POST['selected_product'];
 

        $quantity = $_POST['quantity'];
        $roomNo = $_POST['room_no'];
        $notes = $_POST['notes'];

        $the_product=get_product_details($selectedProduct);
        $the_price=$the_product['price'];
        $totalAmount=($the_price * $quantity);

        // Check if the selected product is not empty
        if (!empty($selectedProduct)) {
            // Save the order in the database using the save_order() function
             echo $totalAmount; 
            save_order($selectedProduct, $quantity, $roomNo, $notes, $totalAmount);
            echo "<h4 class='text-success'>saved</h4>";
        } 
    }else{
        echo "<h4 class='text-danger'>Please select a product before placing the order.</h4>";

    }
}

?>

<div class="container">
    <h1>Welcome to the Cafeteria</h1>

    <div class="row">
        <?php
        $products = get_products();
        if (count($products) == 0) {
            echo "There are no products.";
        } else {
            ?>
            <div class="row d-flex">
                <div class="col-9">
                    <div class="container">
                        <div class="row d-flex ">
                            <div class="col-6">
                                <h1 class='text-primary'>Order Form</h1>
                                <div class="container border border-primary w-50 p-3">
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <label for="selected_product">Selected Product:</label>
                                            <!-- <input type="text" id="selected_product" name="selected_product" class="form-control" readonly> -->
                                           
                                            <div class="form-group">
                                            <select class="form-control" id="selected_product" name="selected_product">
                                                <option selected></option>
                                           
                                                  <?php foreach ($products as $product) { ?>
                                                    <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                              

                                            <div class="mb-3">
                                                <label for="quantity">Quantity:</label>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-primary quantity-btn" data-action="decrement">-</button>
                                                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                                                    <button type="button" class="btn btn-primary quantity-btn" data-action="increment">+</button>
                                                </div>
                                            </div>
                                            <label for="room_no">Room:</label>
                                            <!-- <select class="form-control w-50" id="room_no" name="room_no" required>
                                                <option selected></option>
                                                <option value='combobox'>Combobox</option>
                                            </select> -->
                                            <input type="number" id="" name="room_no" class="form-control" >

                                            <label for="notes">Notes:</label>
                                            <textarea name="notes" id="notes" class="form-control"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label type="number" id="total_amount" class="form-control" >
                                                <button type="submit" name="submit_order" class="btn btn-primary">Confirm Order</button>
                                            </div>
                                        </form>
                                </div>
                            </div>
                            <div class="col-6">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <?php foreach ($products as $product) { ?>
                        <div class="card">
                            <img src="images/<?php echo $product['image_url']; ?>" class="card-img-top w-50" alt="Product Image" onclick="addToCart('<?php echo $product['name']; ?>', '<?php echo $product['price']; ?>')">

                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p>Price: <?php echo $product['price']; ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php
        }
        ?>

    </div>
</div>

<script>
    function addToCart(productName, productPrice) {
       
        document.getElementById('selected_product').value = productName;
        // updateTotalAmount();

        // Increment or decrement quantity
        document.querySelectorAll('.quantity-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var action = this.getAttribute('data-action');
                var quantityInput = document.getElementById('quantity');
                var quantity = parseInt(quantityInput.value);

                if (action === 'increment') {
                    quantity += 1;
                } else if (action === 'decrement') {
                    if (quantity > 1) {
                        quantity -= 1;
                    }
                }

                quantityInput.value = quantity;
                // updateTotalAmount();
            });
        });

        // Update total amount based on quantity and product price
        // function updateTotalAmount() {
        //     var quantity = parseInt(document.getElementById('quantity').value);
        //     var price = parseFloat(productPrice);
        //     var totalAmount = quantity * price;

        //     document.getElementById('total_amount').value = totalAmount.toFixed(2);
        // }
    }
</script>
<?php

?>

<?php
include 'includes/footer.php';
?>
