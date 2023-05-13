<?php
require_once('../includes/config.php');
require_once('../includes/functions.php');
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Check if the user is logged in and is an admin
// check_session();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $image = $_FILES['image'];

    // Validate the form data
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($price)) {
        $errors[] = "Price is required.";
    } elseif (!is_numeric($price)) {
        $errors[] = "Price must be a numeric value.";
    }

    if (empty($category_id)) {
        $errors[] = "Category is required.";
    } elseif (!is_category_exists($category_id)) {
        $errors[] = "Invalid category.";
    }

    if (!empty($image['name'])) {
        // Process the image upload
        $upload_dir = '../product_images/';
        $image_name = basename($image['name']);
        $target_path = $upload_dir . $image_name;

        $image_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');

        if (!in_array($image_type, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF images are allowed.";
        }

        if (empty($errors)) {
            if (move_uploaded_file($image['tmp_name'], $target_path)) {
                // Image uploaded successfully, update the database
                $query = "UPDATE products SET name = :name, price = :price, category_id = :category_id, image_url = :image_url WHERE id = :id";

                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':image_url', $target_path);
                $stmt->bindParam(':id', $id);

                if ($stmt->execute()) {
                    // Redirect to the products page
                    header('Location: products.php');
                    exit();
                } else {
                    $errors[] = "An error occurred while updating the product. Please try again later.";
                }
            } else {
                $errors[] = "Failed to upload the image. Please try again.";
            }
        }
    } else {
        // No image uploaded, update the database without the image
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
            $errors[] = "An error occurred while updating the product. Please try again later.";
        }
    }
}

// Get the product details from the database
$id = (int)$_GET['id'];
$product = get_product_by_id($id);

// Get all categories from the database
$categories = get_all_categories();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
  <?php include 'nav-admin.php' ?> 
    <div class="container">
        <h1 class="my-4">Edit Product</h1>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error) {
                    echo "<p>$error</p>";
                } ?>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo $product['name']; ?>">
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" name="price" id="price" class="form-control" value="<?php echo $product['price']; ?>">
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category_id" id="category" class="form-control">
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] === $product['category_id']) {
                            echo 'selected';
                        } ?>><?php echo $category['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Current Image:</label>
                    <br>
                    <?php if (!empty($product['image_url'])) { ?>
                        <img style="width:100px" src="<?php echo $product['image_url']; ?>" alt="Product Image" class="product-image">
                    <?php } else { ?>
                        <span>No image available</span>
                        <?php } ?>
                    </div>


            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
