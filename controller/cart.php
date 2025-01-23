<?php
require "../model/orm_v2.php";
session_start();
// session_destroy();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
$error = [];
if (!$_SESSION['user_id']) {
    header('location:login.php');
    exit();
}


// إزالة منتج من السلة
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $db->delete('cart', ["product_id" => $product_id]);
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit;
}

// تحديث الكمية
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $stock = $db->get("select stock from products where  id = $product_id");
        if ($quantity > 0) {
            if ($quantity <= $stock[0]['stock'])
                $db->update('cart', ['quantity' => $quantity], ["product_id" => $product_id]);
            else
                $error['stock'] = "Available only $stock ";
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header("Location: cart.php");
    exit;
}

// جلب تفاصيل المنتجات من السلة
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    $cart_items = $db->get("SELECT c.*, p.* FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = $user_id
          ");
}
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
    <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="index.php">E-commerce</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto py-4 py-lg-0">
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="cart.php">MyCart</a></li>
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <?php if (isset($error['stock'])) { ?>
            <div><?= $error['stock'];
        } ?></div>
        <h1 class="text-center">Your Cart</h1>
        <form method="POST" action="">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grand_total = 0; ?>
                    <?php foreach ($cart_items as $item):
                        print_r($item) ?>
                        <?php
                        $product_id = $item['product_id'];
                        $stock = $item['stock'];
                        $quantity = $item['quantity'];
                        $total = $item['price'] * $quantity;
                        $grand_total += $total;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td>$<?= number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" name="quantities[<?= $product_id; ?>]" value="<?= $quantity; ?>"
                                    min="1" max="<?= $stock ?>" class="form-control">
                            </td>
                            <td>$<?= number_format($total, 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $product_id; ?>" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
                <h3>Total: $<?= number_format($grand_total, 2); ?></h3>
                <div>
                    <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>