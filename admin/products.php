<?php
require ('../includes/DB_class.php');
require_once('../includes/functions.php');

$pdo = DataBase::getPDO();

$query = "SELECT * FROM products";
$stmt = $pdo->query($query);

if ($stmt->rowCount() == 0) {
    $message = "There are no products available.";
} else {
    $table = "<table class='table table-striped'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $product['id'];
        $name = $product['name'];
        $price = $product['price'];
        $category_id = $product['category_id'];
        $image_filename = basename($product['image_url']);

        $query2 = "SELECT * FROM categories WHERE id = :category_id";
        $stmt2 = $pdo->prepare($query2);
        $stmt2->bindParam(':category_id', $category_id);
        $stmt2->execute();
        $category = $stmt2->fetch(PDO::FETCH_ASSOC);
        $category_name = $category['name'];

        $table .= "<tr>
                      <td>$id</td>
                      <td><img src='../product_images/$image_filename' alt='$name' class='product-image'></td>
                      <td>$name</td>
                      <td>$price</td>
                      <td>$category_name</td>
                      <td>
                          <a href='edit_product.php?id=$id' class='btn btn-primary'>Edit</a>
                          <a href='delete_product.php?id=$id' class='btn btn-danger'>Delete</a>
                      </td>
                  </tr>";
    }
    $table .= "</tbody></table>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
  <?php include 'nav-admin.php' ?> 
    <div class="container">
        <h1 class="my-4 text-center">Products</h1>

        <?php if (!empty($message)) { ?>
            <p><?php echo $message; ?></p>
        <?php } else { ?>
            <?php echo $table; ?>
        <?php } ?>
         <div class="d-flex justify-content-center">
        <a href="add_new_product.php" class="btn btn-success mt-3 mx-auto">Add Product</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
