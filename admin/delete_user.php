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


// Get the user id from the database
$id = (int) $_GET['id'];
$user = get_user_by_id($id);

if(empty($user)){
    echo "not exist";
}else{
    
    echo " exist you can delete";
}
$query = "DELETE FROM users WHERE id=$id";
$stmt = $pdo->prepare($query);
if ($stmt->execute()) {
    // Redirect to the users page
    // header('Location: users.php');
    // exit();
    echo "tmam deleted ya fandm";
} else {
    $error_message = 'An error occurred while deleting the user. Please try again later.';
}

?>
<h1>delete user</h1>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>


<?php
// Include the footer
include_once('../includes/footer.php');
?>