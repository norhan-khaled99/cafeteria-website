<?php
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css' rel='stylesheet'
        integrity='sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp' crossorigin='anonymous'>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js'></script>
        <link rel='stylesheet' href='css/nav-style.css'>
        ";
?>

<nav class="navbar navbar-expand-lg wheat ">
    <div class="container">
        <a class="navbar-brand fw-bold fs-1 " href="index.php">Cafeteria</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link fs-4" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-4" href="orders.php">My Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-4 ms-5" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
