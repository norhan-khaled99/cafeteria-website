<?php
include '../includes/DB_class.php';
require_once('../includes/functions.php');

// Create a new PDO instance
// $pdo = DataBase::connect();
// Retrieve the PDO instance
$pdo = DataBase::getPDO();


// Create the necessary tables
create_tables($pdo);

// Check if the user is logged in and is an admin
check_session();

include 'nav-admin.php';

// Get all existing users
$users = get_all_users();

// Initialize variables for date filtering and user selection
$dateFrom = '';
$dateTo = '';
$selectedUser = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected dates and user
    $dateFrom = $_POST['date_from'];
    $dateTo = $_POST['date_to'];
    $selectedUser = $_POST['user_id'];
}

// Retrieve checks based on date and user selection
// Call the get_checks() function with the correct PDO instance
$checks = get_checks($pdo, $dateFrom, $dateTo, $selectedUser);

?>

<div class="container">
    <h1 class="text-center my-2">Checks</h1>

    <!-- Date Selection -->
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <form method="POST">
                <div class="form-group">
                    <label for="date_from">From:</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $dateFrom; ?>">
                </div>
                <div class="form-group">
                    <label for="date_to">To:</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $dateTo; ?>">
                </div>
                <div class="form-group">
                    <label for="user_id">Select User:</label>
                    <select class="form-control" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo ($selectedUser == $user['id']) ? 'selected' : ''; ?>><?php echo $user['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="d-flex justify-content-center my-1">
                <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Checks and Order Information -->
    <div class="row mt-4">
        <div class="col-md-12">
            <?php if (count($checks) > 0) { ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checks as $check) { ?>
                            <tr>
                                <td><?php echo $check['date']; ?></td>
                                <td><?php echo $check['user']; ?></td>
                                <td><?php echo $check['total_amount']; ?></td>
                                <td>
                                    <button class="btn btn-primary view-order-btn" data-check-id="<?php echo $check['id']; ?>" data-toggle="modal" data-target="#orderModal">View Order</button>
                                </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No checks found.</p>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Order details will be displayed here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to display order details in the modal
    function showOrderDetails(checkId) {
        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_order_details.php?check_id=' + checkId, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Process the response from the server
                var orderDetails = JSON.parse(xhr.responseText);

                // Build the HTML content for order details
                var html = '<h5>Order Items</h5>';
                html += '<table class="table">';
                html += '<thead><tr><th>Product</th><th>Quantity</th><th>Price</th></tr></thead>';
                html += '<tbody>';
                for (var i = 0; i < orderDetails.length; i++) {
                    html += '<tr>';
                    html += '<td>' + orderDetails[i].product + '</td>';
                    html += '<td>' + orderDetails[i].quantity + '</td>';
                    html += '<td>$' + orderDetails[i].price + '</td>';
                    html += '</tr>';
                }
                html += '</tbody></table>';

                // Set the HTML content in the modal body
                document.getElementById('orderDetails').innerHTML = html;
            }
        };

        xhr.send();
    }

    // Event listener for view order button
    var viewOrderButtons = document.querySelectorAll('.view-order-btn');
    viewOrderButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var checkId = button.getAttribute('data-check-id');
            showOrderDetails(checkId);
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
