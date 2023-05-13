<?php
include '../includes/DB_class.php';
require_once('../includes/functions.php');

// Create a new PDO instance
$pdo = DataBase::connect();

// Create the necessary tables
create_tables($pdo);

// Check if the user is logged in and is an admin
check_session();

include 'nav-admin.php';
?>

<div class="container">
    <h1>Place Order for User</h1>

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
                            <div class="card product-card" data-product-id="<?php echo $product['id']; ?>">
                                <img src="../images/<?php echo $product['image_url']; ?>" class="card-img-top" alt="Product Image">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                    <p class="card-text">Price: $<?php echo $product['price']; ?></p>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-primary quantity-btn" data-action="decrement">-</button>
                                        <input type="number" class="form-control quantity-input" value="1" min="1">
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
            <div class="card">
                <div class="card-header">Selected Items</div>
                <div class="card-body" id="selected-items">
                    <p>No items selected.</p>
                </div>
                <div class="card-footer">
                    <p>Total Amount: $<span id="total-amount">0.00</span></p>
                    <button type="button" class="btn btn-primary btn-block" id="confirm-order-btn" disabled>Confirm Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to product cards
        var productCards = document.querySelectorAll('.product-card');
        productCards.forEach(function(card) {
            card.addEventListener('click', function() {
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
                quantityButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
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
                removeButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
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

            selectedItems.forEach(function(item) {
                var itemPrice = parseFloat(item.querySelector('.selected-item-price').textContent.replace('$', ''));
                var itemQuantity = parseInt(item.querySelector('.selected-item-quantity').textContent);
                totalAmount += itemPrice * itemQuantity;
            });

            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        // Remove selected item from the list
        function removeSelectedItem(item) {
            item.parentNode.removeChild(item);
            updateTotalAmount();

            // Disable the confirm order button if no items are selected
            if (document.querySelectorAll('.selected-item').length === 0) {
                document.getElementById('confirm-order-btn').disabled = true;
            }
        }

        // Event listener for confirm order button
        var confirmOrderBtn = document.getElementById('confirm-order-btn');
        confirmOrderBtn.addEventListener('click', function() {
            var selectedItems = document.querySelectorAll('.selected-item');
            var orders = [];

            selectedItems.forEach(function(item) {
                var productId = item.getAttribute('data-product-id');
                var productName = item.querySelector('.selected-item-name').textContent;
                var productPrice = parseFloat(item.querySelector('.selected-item-price').textContent.replace('$', ''));
                var quantity = parseInt(item.querySelector('.selected-item-quantity').textContent);

                var order = {
                    product_id: productId,
                    product_name: productName,
                    price: productPrice,
                    quantity: quantity
                };

                orders.push(order);
            });

            // Send the orders to the server for processing
            sendOrders(orders);
        });

        // AJAX request to send the orders to the server
        function sendOrders(orders) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process_order.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Orders successfully processed
                        alert('Orders placed successfully!');
                        // Reset the selected items
                        document.getElementById('selected-items').innerHTML = '<p>No items selected.</p>';
                        // Disable the confirm order button
                        confirmOrderBtn.disabled = true;
                        // Reset the total amount
                        document.getElementById('total-amount').textContent = '0.00';
                    } else {
                        // Error occurred while processing orders
                        alert('Error occurred while placing orders.');
                    }
                }
            };

            var data = JSON.stringify(orders);
            xhr.send(data);
        }
    });
</script>

<?php
include '../includes/footer.php';
?>
