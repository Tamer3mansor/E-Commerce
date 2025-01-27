<?php
session_start();
require "../model/orm_v2.php";

// session_destroy();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();

if (!$_SESSION['user_id'] || ($_SESSION['role'] != 'customer')) {
    header('location:login.php');
    exit();
}

// Get cart items for the logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT c.*, p.name, p.price FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = $user_id";

$result = $db->get($query);

// Calculate total price
$total_price = 0;
$cart_items = [];
foreach ($result as $row) {
    $total_price += $row['quantity'] * $row['price'];
    $cart_items[] = $row;
}
require "../views/dist/checkout.html";
?>