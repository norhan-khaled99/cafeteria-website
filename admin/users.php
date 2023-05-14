<?php
include('../includes/config.php');
require_once('../includes/functions.php');
// $pdo = new PDO("mysql:host=$host;", $username, $password);
// $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// var_dump(is_admin());
if (!is_admin()) {
    redirect('index.php');
    // echo "not admin";
}

// Get all users
$query = "SELECT * FROM users";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users - Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
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
<?php include 'nav-admin.php' ?> 

    <main>
        <div class="container">
            <h1 class="my-4 text-center">Users</h1>
            <?php if (count($users) == 0) { ?>
                <p>There are no users.</p>
            <?php } else { ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Room No</th>
                            <th>Ext</th>
                            <th>Profile Picture</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo isset($user['room_no']) ? $user['room_no'] : ''; ?></td>
                                <td><?php echo isset($user['ext']) ? $user['ext'] : ''; ?></td>
                                <td>
                                    <?php if (!empty($user['profile_picture'])) { ?>
                                        <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" width="50">
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
            <div class="d-flex justify-content-center my-3">
            <a href="add_user.php" class="btn btn-success">Add User</a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
