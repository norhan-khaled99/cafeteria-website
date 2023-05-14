<?php
require 'includes/functions.php';

if (!empty($_GET)) {
    $email = $_GET['email'];
    var_dump($email);
    $user = get_user_by_email($email);
    if ($user) {
        $token = bin2hex(random_bytes(32)); 

        DataBase::update_user_token($email,$token);
        echo 'ay btngaaan';
        // header("Location: login.php");

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