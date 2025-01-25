<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
$error = [];
if (!$_SESSION['user_id']) {
    header('location:login.php');
    exit();
}
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// جلب تفاصيل الطلب من قاعدة البيانات
$query = "SELECT o.total_price, oi.product_id, oi.quantity, oi.price, p.name 
              FROM orders o
              JOIN order_details oi ON o.id = oi.order_id
              JOIN products p ON oi.product_id = p.id
              WHERE o.id =  $order_id ";

$result = $db->get(query: $query);


// عرض تفاصيل الطلب
$order = $result[0];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Start of Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="index.php">E-Commerce</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto py-4 py-lg-0">
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="cart.php">MyCart</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="orders.php">My Orders</a></li>
                        <?php if(isset($_SESSION['user_id'])) {  ?>
                            <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="logout.php">LogOut</a></li>
                            <li class="nav-link px-lg-3 py-3 py-lg-4"> <?= ($_SESSION['username']);?></li>     
                       <?php }else{ ?>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="login.php">Login</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    <!-- End of Navbar -->

    <div class="container mt-5">
        <?php if (isset($error['stock'])): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($error['stock']); ?></div>
        <?php endif; ?>
        <h1 class="text-center">Your Order details</h1>
        <table class="table table-striped table-bordered table-responsive-sm">
            <thead class="table-dark">

                <tr>
                    <th> product ame</th>
                    <th>product Price</th>
                    <th>quantity</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($result as $order) { ?>
                    <tr>
                        <td><?= htmlspecialchars($order['name']); ?></td>
                        <td><?= number_format($order['price'], 2); ?></td>
                        <td><?= htmlspecialchars($order['quantity']); ?></td>
                    </tr>
                    <tr>total price = <?= $order['total_price'] ?></tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>