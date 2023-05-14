<?php
require_once('../includes/functions.php');
$pdo = DataBase::getPDO();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $room_no = $_POST['room_no'];
    $ext = $_POST['ext'];

    if ($password && $password != $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = 'Email already exists';
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);

            $profilePicture = $_FILES['profile_picture'];
            $profilePictureName = $profilePicture['name'];
            $profilePictureTmpName = $profilePicture['tmp_name'];

            $profilePictureExtension = pathinfo($profilePictureName, PATHINFO_EXTENSION);
            $profilePictureFilename = date('YmdHis') . '.' . $profilePictureExtension;

            $uploadDir = '../profile_pictures/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $profilePicturePath = $uploadDir . $profilePictureFilename;
            move_uploaded_file($profilePictureTmpName, $profilePicturePath);

            $query = "INSERT INTO users (name, email, password, room_no, ext, profile_picture) VALUES (:name, :email, :password, :room_no, :ext, :profile_picture)";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':room_no', $room_no);
            $stmt->bindValue(':ext', $ext);
            $stmt->bindValue(':profile_picture', $profilePicturePath);

            if ($stmt->execute()) {
                $success = 'User added successfully';
                header('Location: users.php');
                exit();
            } else {
                $error = 'Error adding user: ' . $stmt->errorInfo()[2];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add User - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<?php include 'nav-admin.php' ?> 

    <h1>Add User</h1>
    <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success)) : ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control">
        </div>
        <div class="form-group">
            <label>Room Number</label>
            <input type="text" name="room_no" class="form-control">
        </div>
        <div class="form-group">
            <label>Extension</label>
            <input type="text" name="ext" class="form-control">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </form>

    <?php include '../includes/footer.php'; ?>
</body>

</html>
