<?php
include_once('../includes/config.php');
include_once('../includes/functions.php');
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Check if the user is logged in as admin
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit();
} elseif (!is_admin()) {
    header('Location: index.php');
    exit();
}

// Get the user id from the URL parameter
$id = (int)$_GET['id'];

// Check if the user exists
$user = get_user_by_id($id);
if (empty($user)) {
    echo "User does not exist.";
} else {
    // Perform the delete operation
    $query = "DELETE FROM users WHERE id=:id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        echo "User deleted successfully.";
        // Redirect to the users page after deletion
        header('Location: users.php');
        exit();
    } else {
        $error_message = 'An error occurred while deleting the user. Please try again later.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete User - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<?php include 'nav-admin.php' ?> 

    <h1>Delete User</h1>
    <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php include_once('../includes/footer.php'); ?>
</body>

</html>
