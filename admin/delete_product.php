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

// Get the product id from the database
$id = (int) $_GET['id'];
$product = get_product_by_id($id);

if(empty($product)){
    echo "not exist";
}else{
    
    echo " exist you can delete";
}
$query = "DELETE FROM products WHERE id=$id";
$stmt = $pdo->prepare($query);
if ($stmt->execute()) {
    // Redirect to the products page
    // header('Location: products.php');
    // exit();
    echo "tmam deleted ya fandm";
} else {
    $error_message = 'An error occurred while updating the product. Please try again later.';
}
// Include the header
?>
<h1>delete Product</h1>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>


<?php
// Include the footer
include_once('../includes/footer.php');
?>