<?php
require_once('../includes/functions.php');
$pdo = DataBase::getPDO();
// Create the necessary tables
// create_tables($pdo);

// Check if the user is logged in and is an admin
check_session();

include 'nav-admin.php';

// Get all users from the database
$users = get_all_users();

?>

<div class="container me-5">
    <p class="fs-1 text-center">Place Order for User</p> 

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <?php
                // Get all products from the database
                $products = get_products();
if (count($products) == 0) {
    echo "There are no products.";
} else {
    foreach ($products as $product) {
        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card product-card me-5 rounded" data-product-id="<?php echo $product['id']; ?>">
                                <img class="w-100" src="../images/<?php echo $product['image_url']; ?>" class="card-img-top" alt="Product Image">
                                <div class="card-body">
                                    <h5 class="card-title text-center"><?php echo $product['name']; ?></h5>
                                    <p class="card-text text-center">Price: $<?php echo $product['price']; ?></p>
                                    <div class="input-group d-flex justify-content-center">
                                        <button type="button" class="btn btn-primary quantity-btn" data-action="decrement">-</button>
                                        <input type="number" class="form-control quantity-input text-center" value="1" min="1">
                                        <button type="button" class="btn btn-primary quantity-btn" data-action="increment">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
    }
}
?>
            </div>
        </div>
        <div class="col-md-4">
            <form action="process_order.php" method="post">
                <div class="card text-center ">
                    <div class="card-header">Selected Items</div>
                    <div class="card-body" id="selected-items">
                        <p>No items selected.</p>
                    </div>
                    <div class="card-footer">
                        <p>Total Amount: $<span id="total-amount">0.00</span></p>
                        <div class="form-group">
                            <label for="user_id">Select User:</label>
                            <select class="form-control" id="user-select" name="user_id">
                                <?php foreach ($users as $user) { ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary btn-block my-3" id="confirm-order-btn" disabled>Confirm Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

 <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add event listeners to product cards
        var productCards = document.querySelectorAll('.product-card');
        productCards.forEach(function (card) {
            card.addEventListener('click', function () {
                var productId = card.getAttribute('data-product-id');
                var productName = card.querySelector('.card-title').textContent;
                var productPrice = parseFloat(card.querySelector('.card-text').textContent.replace('Price: $', ''));
                var quantity = parseInt(card.querySelector('.quantity-input').value);

                var item = document.createElement('div');
                item.className = 'selected-item mb-2';
                item.setAttribute('data-product-id', productId);
                item.innerHTML = `
                <div class="row">
                    <div class="col-6">
                        <span class="selected-item-name">${productName}</span>
                    </div>
                    <div class="col-4">
                        <span class="selected-item-price">$${productPrice.toFixed(2)}</span>
                    </div>
                    <div class="col-2">
                        <span class="selected-item-quantity">${quantity}</span>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-btn">Remove</button>
                `;

                document.getElementById('selected-items').appendChild(item);
                updateTotalAmount();

                // Enable the confirm order button
                document.getElementById('confirm-order-btn').disabled = false;

                // Event listener for quantity buttons
                var quantityButtons = document.querySelectorAll('.quantity-btn');
                quantityButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        var action = button.getAttribute('data-action');
                        var quantityInput = button.parentNode.querySelector('.quantity-input');
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

                // Event listener for remove buttons
                var removeButtons = document.querySelectorAll('.remove-btn');
                removeButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        var item = button.parentNode;
                        removeSelectedItem(item);
                    });
                });
            });
        });

        // Update the total amount
        function updateTotalAmount() {
            var totalAmount = 0;
            var selectedItems = document.querySelectorAll('.selected-item');

            selectedItems.forEach(function (item) {
                var itemPrice = parseFloat(item.querySelector('.selected-item-price').textContent.replace('$', ''));
                var itemQuantity = parseInt(item.querySelector('.selected-item-quantity').textContent);
                totalAmount += itemPrice * itemQuantity;
            });

            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        function removeSelectedItem(item) {
            if (item && item.parentNode) {
                item.parentNode.removeChild(item);
                updateTotalAmount();

                // Disable the confirm order button if no items are selected
                if (document.querySelectorAll('.selected-item').length === 0) {
                    document.getElementById('confirm-order-btn').disabled = true;
                }
            }
        }

        // Event listener for confirm order button
        var confirmOrderBtn = document.getElementById('confirm-order-btn');
        confirmOrderBtn.addEventListener('click', function () {
            var selectedItems = document.querySelectorAll('.selected-item');
            var orders = [];

            selectedItems.forEach(function (item) {
                var productId = item.getAttribute('data-product-id');
                var productName = item.querySelector('.selected-item-name').textContent;
                var productPrice = parseFloat(item.querySelector('.selected-item-price').textContent.replace('$', ''));
                var itemQuantity = parseInt(item.querySelector('.selected-item-quantity').textContent);
                var order = {
                    productId: productId,
                    productName: productName,
                    productPrice: productPrice,
                    quantity: itemQuantity
                };
                orders.push(order);
            });

            var userId = document.getElementById('user-select').value;

            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();
           
            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process_order.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Process the response from the server
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Order processed successfully
                        alert('Order confirmed!');
                        // Clear the selected items list
                        document.getElementById('selected-items').innerHTML = '';
                        // Disable the confirm order button
                        document.getElementById('confirm-order-btn').disabled = true;
                        // Reset the total amount
                        document.getElementById('total-amount').textContent = '0.00';
                    } else {
                        // Order processing failed
                        alert('Failed to confirm order. Please try again.');
                    }
                }
            };

            var data = JSON.stringify({
                orders: orders,
                userId: userId
            });

            xhr.send(data);
        });
    });

    // Function to retrieve the user ID (Replace this with your actual logic)
    function getUserId() {
        return 'user123'; // Replace with your actual user ID retrieval logic
    }
</script>

<?php
include '../includes/footer.php';
?>
