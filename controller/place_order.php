<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
$error = [];
if (!$_SESSION['user_id'] || ($_SESSION['role'] != 'customer')) {
    header('location:login.php');
    exit();
}
// التحقق من أن البيانات موجودة
if (!isset($_POST['Address']) || empty($_POST['zip']) || empty($_POST['card_number']) || empty($_POST['card_name'])) {
    $user_id = $_SESSION['user_id'];
    header("location:checkout.php?<?=$user_id?>");
}

$shipping_address = $_POST['Address'];
$second_address = $_POST['Address2'];
$zip = $_POST['zip'];
$card_number = $_POST['card_number'];
$card_name = $_POST['card_name'];
$user_id = $_SESSION['user_id'];
$total_price = 0;
$cart_items = [];

// جلب العناصر من السلة
$query = "SELECT c.*, p.name, p.price FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = $user_id";
;
$result = $db->get($query);

$total_price = 0;
$cart_items = [];
foreach ($result as $row) {
    $total_price += $row['quantity'] * $row['price'];
    $cart_items[] = $row;
}


$order_id = $db->insert('orders', ["user_id" => $user_id, "total_price" => $total_price, "shipping_address" => $shipping_address, "second_address" => $second_address, "zip" => $zip, "card_number" => $card_number]);  // الحصول على معرف الطلب الجديد

foreach ($cart_items as $item) {
    $order_id = $db->insert('order_details', ["order_id" => $order_id, "product_id" => $item['product_id'], "quantity" => $item['quantity'], "price" => $item['price']]);  // الحصول على معرف الطلب الجديد
}

$query = "DELETE FROM cart WHERE user_id = ? OR session_id = ?";
$db->delete('cart', ["user_id" => $user_id]);

header("Location: orders.php?order_id=" . $order_id);
exit;
?>