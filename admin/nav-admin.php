<?php
echo "
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css' rel='stylesheet'
integrity='sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp' crossorigin='anonymous'>
<style>
    .navbar {
        background-color: #333;
    }

    .navbar-brand {
        color: #fff;
        font-size: 24px;
        font-weight: 600;
    }

    .navbar-nav .nav-link {
        color: #fff;
        font-size: 18px;
    }

    .navbar-nav .nav-link:hover {
        color: wheat;
    }
</style>
";
?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./admin-order.php">Manual Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./checks.php">Checks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
