<?php
require_once 'includes/DB_class.php';
require_once 'includes/functions.php';

if (!empty($_GET)) {
    $email = $_GET['email'];
    $user = get_user_by_email($email); // Use the function from functions.php
    if ($user) {
        $token = bin2hex(random_bytes(32)); // Generate a unique token

        // Store the token, user ID, and expiration date in the database
        // $host = 'localhost';
        // $dbname = 'cafeteriaWebsiteDB';
        // $username = 'root';
        // $password = 'pass';
        $host = 'localhost';
        $dbname = 'cafeteria_db';
        $username = 'phpuser';
        $password = 'Iti123456';

        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $stmt = $db->prepare("UPDATE users SET reset_token = :token WHERE id = :id");
        $stmt->execute(array(':token' => $token, ':id' => $user['id']));
        $result = $stmt->rowCount();
    } else {
        $error = "Invalid email address.";
    }
}

?>

<?php
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css' rel='stylesheet'
        integrity='sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp' crossorigin='anonymous'>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js'></script>
        <link rel='stylesheet' href='css/login.css'>
        ";
?>
<!-- Display the form to request a password reset -->
<div class="container">
    <br>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <?php if (!empty($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>

    <div class="container d-flex justify-content-left">
        <form class="w-50" action="update-password.php?token=<?php echo $token ?>" method="POST">
        <h1 class="text-light">Hi <?php echo $user['name']?><br>&nbsp; please,reset your password</h1>    
        <label for="email" class="form-label" style="color:wheat;">enter new password</label>
            <input type="password" name="password_new" class="form-control" required>
            <div class="mt-3 text-center">
                <button type="submit" class="btn text-center mb-3 wheat">Reset password</button>
            </div>
        </form>
    </div>
</div>