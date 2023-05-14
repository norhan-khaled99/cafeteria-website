<?php
require_once('../includes/functions.php');
$pdo = DataBase::getPDO();

if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $errors = validate_category_form($name);

    if (count($errors) == 0) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Category added successfully';
            echo "doneee";
        } else {
            $_SESSION['error_message'] = 'Error adding category';
        }
    }
}
?>
  <?php include 'nav-admin.php' ?> 

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
