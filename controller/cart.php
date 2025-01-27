<?php
require "../model/orm_v2.php";
session_start();
// session_destroy();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
$error = [];
if (!$_SESSION['user_id'] ||($_SESSION['role']!='customer') ) {
    header('location:login.php');
    exit();
}

// جلب تفاصيل المنتجات من السلة
$cart_items = [];

$user_id = $_SESSION['user_id'];
$cart_items = $db->get("SELECT c.*, p.* FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = $user_id
          ");

// إزالة منتج من السلة
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $db->delete('cart', ["product_id" => $product_id]);
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
require "../views/dist/cart.html";
?>
