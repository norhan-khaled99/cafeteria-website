<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
// session_start();
$pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

// Handle form submission
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    // Validate form data
    $errors = validate_category_form($name);

    // If there are no errors, add the category to the database
    if (count($errors) == 0) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            // Category added successfully
            $_SESSION['success_message'] = 'Category added successfully';
            // redirect('categories.php');
            echo "doneee";
        } else {
            // Error adding category
            $_SESSION['error_message'] = 'Error adding category';
        }
    }
}
?>
<h1>Add Category</h1>
<?php
    if (isset($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>">
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Add Category</button>
</form>
<?php include_once '../includes/footer.php'; ?>