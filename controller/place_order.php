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
// التحقق من أن البيانات موجودة
if (!isset($_POST['shipping_address']) || empty($_POST['shipping_address'])) {
    die("Shipping address is required.");
}

$shipping_address = $_POST['shipping_address'];
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

// إنشاء طلب جديد في جدول orders
// $query = "INSERT INTO orders (user_id, total_price, shipping_address) VALUES ( $user_id, $total_price, $shipping_address)";

$order_id = $db->insert('orders', ["user_id" => $user_id, "total_price" => $total_price, "shipping_address" => $shipping_address]);  // الحصول على معرف الطلب الجديد

// إضافة العناصر إلى جدول order_items
foreach ($cart_items as $item) {
    $order_id = $db->insert('order_details', ["order_id" => $order_id, "product_id" => $item['product_id'], "quantity" => $item['quantity'], "price" => $item['price']]);  // الحصول على معرف الطلب الجديد
}

// حذف العناصر من السلة بعد إتمام الطلب
$query = "DELETE FROM cart WHERE user_id = ? OR session_id = ?";
$db->delete('cart', ["user_id" => $user_id]);

// إعادة توجيه المستخدم إلى صفحة تأكيد الطلب
header("Location: order_confirmation.php?order_id=" . $order_id);
exit;
?>