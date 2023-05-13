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

// Retrieve product details from the database
// $products = get_product_details();

if (isset($_POST['submit_order'])) {
    // Retrieve the form values
    $selectedProduct = isset($_POST['selected_product']) ? $_POST['selected_product'] : '';
    $quantity = $_POST['quantity'];
    $roomNo = $_POST['room_no'];
    $notes = $_POST['notes'];
    $totalAmount = $_POST['total_amount'];

    // Check if the selected product is not empty
    if (!empty($selectedProduct)) {
        // Get the product details
        $product = get_product_details($selectedProduct);

        // Check if the product exists
        if ($product) {
            // Save the order in the database using the save_order() function
            save_order($selectedProduct, $quantity, $roomNo, $notes, $totalAmount);

            // Redirect to a success page or display a success message
            redirect('order-success.php');
        } else {
            // Handle the case where the selected product does not exist
            echo "Invalid product selected.";
        }
    } else {
        // Handle the case where the selected product is not provided
        echo "Please select a product before placing the order.";
    }
}

?>

<div class="container">
    <h1>Order Form</h1>
    <div class="row">
        <div class="col-md-6">
            <form action="" method="post" id="order-form">
                <div class="mb-3">
                    <label for="selected_product" class="form-label">Selected Product:</label>
                    <input type="text" id="selected_product" class="form-control" name="selected_product" readonly value="<?php echo isset($_POST['selected_product']) ? $_POST['selected_product'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-primary quantity-btn" data-action="decrement">-</button>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                        <button type="button" class="btn btn-primary quantity-btn" data-action="increment">+</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="room_no" class="form-label">Room:</label>
                    <select class="form-control" id="room_no" name="room_no" required>
                        <option value="" selected disabled>Select Room</option>
                        <option value="combobox">Combobox</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes:</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="total_amount" class="form-label">Total Amount:</label>
                    <input type="text" id="total_amount" class="form-control" name="total_amount" readonly>
                </div>

                <div class="mb-3">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="submit_order" class="btn btn-primary">Confirm Order</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <h3>Product Details</h3>
            <div class="card">
                <img src="images/<?php echo isset($_POST['selected_product']) ? $products[$_POST['selected_product']]['image_url'] : ''; ?>" class="card-img-top" alt="Product Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo isset($_POST['selected_product']) ? $products[$_POST['selected_product']]['name'] : ''; ?></h5>
                    <p class="card-text"><?php echo isset($_POST['selected_product']) ? $products[$_POST['selected_product']]['description'] : ''; ?></p>
                    <p class="card-text">Price: <?php echo isset($_POST['selected_product']) ? $products[$_POST['selected_product']]['price'] : ''; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selected_product').value = "<?php echo isset($_POST['selected_product']) ? $products[$_POST['selected_product']]['name'] : ''; ?>";

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
            updateTotalAmount();
        });
    });

    // Update total amount based on quantity and product price
    function updateTotalAmount() {
        var quantity = parseInt(document.getElementById('quantity').value);
        var price = parseFloat(<?php echo isset($_POST['selected_product']) ? $products[$_POST['selected_product']]['price'] : 0; ?>);
        var totalAmount = quantity * price;

        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }

    // Call updateTotalAmount() when quantity value changes
    document.getElementById('quantity').addEventListener('change', updateTotalAmount);

    // Call updateTotalAmount() on page load
    window.addEventListener('load', updateTotalAmount);
</script>
