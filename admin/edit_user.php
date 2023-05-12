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

$id = (int) $_GET['id'];
$user = get_user_by_id($id);
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $room_no = $_POST['room_no'];
    $ext = $_POST['ext'];
    $profile_picture = $_POST['profile_picture'];

    // Check if all fields are filled in
    if (empty($name) || empty($email) || empty($password) || 
    empty($confirm_password) || empty($room_no) ||empty($profile_picture)) {
            $error_message = 'Please fill in all fields';
    } else {
        // Update the user in the database
        $query = "UPDATE users SET name = :name, email = :email, password = :password, room_no = :room_no ,profile_picture= :profile_picture,ext=:ext WHERE id = :id;";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':room_no', $room_no);
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->bindParam(':room_no', $room_no);
        $stmt->bindParam(':ext', $ext);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            // Redirect to the users page
            header('Location: users.php');
            exit();
        } else {
            $error_message = 'An error occurred while updating the user. Please try again later.';
        }
    }
}

// Get the user from the database


?>

<h1>Edit user</h1>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo $user['name']; ?>">
    </div>
    <div class="form-group">
        <label for="email">email:</label>
        <input type="email"  name="email" id="email" class="form-control"
            value="<?php echo $user['email']; ?>">
    </div>
    <div class="form-group">
        <label for="password">password:</label>
        <input type="password" name="password" class="form-control" >

    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" >
    </div>
    <div class="form-group">
        <label>Room Number</label>
        <input type="text" name="room_no" class="form-control" value="<?php echo $user['room_no']; ?>">
    </div>
    <div class="form-group">
        <label>Extension</label>
        <input type="text" name="ext" class="form-control" value="<?php echo $user['ext']; ?>">
    </div>
    <div class="form-group">
        <label>photo</label>
        <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" 
        value="<?php echo $user['profile_picture']; ?>">

    </div>
    <button type="submit" class="btn btn-warning">edit user</button>
</form>

<?php
// Include the footer
include_once('../includes/footer.php');
?>