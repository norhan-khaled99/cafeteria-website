<?php
include_once('../includes/config.php');
include_once('../includes/functions.php');
$pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Check if the user is logged in as admin
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit();
} elseif (!is_admin()) {
    header('Location: index.php');
    exit();
}

// Get the list of categories for the form
$categories = get_all_categories();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $id = (int) $_POST['id'];
    $name = $_POST['name'];
    $price = (float) $_POST['price'];
    $category_id = (int) $_POST['category_id'];

    // Check if all fields are filled in
    if (empty($name) || empty($price) || empty($category_id)) {
        $error_message = 'Please fill in all fields';
    } else {
        // Update the product in the database
        $query = "UPDATE products SET name = :name, price = :price, category_id = :category_id WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            // Redirect to the products page
            header('Location: products.php');
            exit();
        } else {
            $error_message = 'An error occurred while updating the product. Please try again later.';
        }
    }
}

// Get the product from the database
$id = (int) $_GET['id'];
$product = get_product_by_id($id);

?>

<h1>Edit Product</h1>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo $product['name']; ?>">
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            value="<?php echo $product['price']; ?>">
    </div>
    <div class="form-group">
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id" class="form-control">
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"
                <?php if ($category['id'] === $product['category_id']) echo 'selected'; ?>>
                <?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-warning">edit Product</button>
</form>

<?php
// Include the footer
include_once('../includes/footer.php');
?>