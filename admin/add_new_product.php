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

// **********************************************************************************
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $price = (float) $_POST['price'];
    $category_id = (int) $_POST['category_id'];
    $image_url = $_POST['image_url'];

    // Check if all fields are filled in

    var_dump($name);
    var_dump($price);
    // var_dump(empty($category_id));
    // var_dump(empty($image_url));
    if (empty($name) || empty($price) || empty($category_id) || empty($image_url)) {
        $error_message = 'Please fill in all fields';
    } else {
        // insert the product in the database
        $query = "INSERT INTO products (name, price, category_id,image_url)VALUES (:name, :price, :category_id, :image_url)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image_url', $image_url);

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
// $id = (int) $_GET['id'];
// $product = get_product($id);

// Include the header
?>
<h1>Add Product</h1>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" class="form-control" >
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control" >
    </div>
    <div class="form-group">
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id" class="form-control">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>">
                <?php echo $category['name']; ?></option>
            <?php echo $category['name']; ?></option>

            </option>
            <?php endforeach; ?>
        </select>

<input type="file" name="image_url" id="image_url" accept="image/*">

    </div>
    <button type="submit" class="btn btn-primary">Add Product</button>
</form>

<?php
// Include the footer
include_once('../includes/footer.php');
?>