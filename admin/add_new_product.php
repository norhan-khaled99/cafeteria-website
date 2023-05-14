<?php
require ('../includes/DB_class.php');
require_once('../includes/functions.php');
$pdo = DataBase::getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    $image = $_FILES['image'];
    $image_name = $image['name'];
    $image_tmp_name = $image['tmp_name'];
    $image_error = $image['error'];

    if (empty($name) || empty($price) || empty($category_id)) {
        $error_message = 'Please fill in all fields.';
    } elseif ($image_error !== 0) {
        $error_message = 'An error occurred while uploading the image. Please try again.';
    } else {
        $upload_dir = '../product_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_filename = time() . '_' . uniqid() . '.' . $image_extension;

        $image_path = $upload_dir . $image_filename;
        if (move_uploaded_file($image_tmp_name, $image_path)) {
            $query = "INSERT INTO products (name, image_url, price, category_id) VALUES (:name, :image_url, :price, :category_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':image_url', 'localhost/product_images/' . $image_filename);
            $stmt->bindValue(':price', $price);
            $stmt->bindValue(':category_id', $category_id);

            if ($stmt->execute()) {
                // Redirect to the products page
                header('Location: products.php');
                exit();
            } else {
                $error_message = 'An error occurred while adding the product. Please try again later.';
            }
        } else {
            $error_message = 'An error occurred while uploading the image. Please try again.';
        }
    }
}

$query = "SELECT * FROM categories";
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
    <div class="container">
        <h1 class="my-4">Add Product</h1>


<?php if (isset($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>
<a href="./add_category.php">add new category</a>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" required accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
