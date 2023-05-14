<?php
require_once('../includes/functions.php');
$pdo = DataBase::getPDO();


if (!is_logged_in()) {
    header('Location: ../login.php');
    exit();
} elseif (!is_admin()) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];

$user = get_user_by_id($id);
if (empty($user)) {
    echo "User does not exist.";
} else {
    $query = "DELETE FROM users WHERE id=:id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        echo "User deleted successfully.";
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
