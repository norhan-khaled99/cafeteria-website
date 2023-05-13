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

$id = (int)$_GET['id'];
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
    $profile_picture = $_FILES['profile_picture'];

    // Check if all fields are filled in
    if (empty($name) || empty($email) || empty($room_no)) {
        $error_message = 'Please fill in all required fields';
    } else {
        // Update the user in the database
        $query = "UPDATE users SET name = :name, email = :email, room_no = :room_no, ext = :ext";

        // Update the password if provided
        if (!empty($password) && $password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password = :password";
        }

        // Update the profile picture if provided
        if (!empty($profile_picture['name'])) {
            $upload_dir = '../profile_pictures/';
            $upload_file = $upload_dir . basename($profile_picture['name']);

            // Move the uploaded file to the desired location
            if (move_uploaded_file($profile_picture['tmp_name'], $upload_file)) {
                $query .= ", profile_picture = :profile_picture";
            }
        }

        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':room_no', $room_no);
        $stmt->bindParam(':ext', $ext);
        $stmt->bindParam(':id', $id);

        if (!empty($password) && $password === $confirm_password) {
            $stmt->bindParam(':password', $hashed_password);
        }

        if (!empty($profile_picture['name'])) {
            $stmt->bindParam(':profile_picture', $upload_file);
        }

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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css' rel='stylesheet'
        integrity='sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp' crossorigin='anonymous'>    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 500px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        .btn-primary {
            margin-top: 20px;
        }

        .current-picture {
            margin-top: 20px;
        }
    </style>
</head>

<body>
<?php include 'nav-admin.php' ?> 

    <div class="container">
        <h1>Edit User</h1>

        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo $user['name']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo $user['email']; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
            </div>
            <div class="form-group">
                <label for="room_no">Room Number:</label>
                <input type="text" name="room_no" id="room_no" class="form-control" value="<?php echo $user['room_no']; ?>">
            </div>
            <div class="form-group">
                <label for="ext">Extension:</label>
                <input type="text" name="ext" id="ext" class="form-control" value="<?php echo $user['ext']; ?>">
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control">
            </div>
            <div class="form-group current-picture">
                <label for="current_picture">Current Picture:</label>
                <?php if (!empty($user['profile_picture'])) : ?>
                    <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" width="100">
                <?php else : ?>
                    <p>No profile picture available</p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Edit User</button>
        </form>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js'></script></body>

</html>
