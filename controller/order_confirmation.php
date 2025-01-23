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
// التحقق من وجود معرف الطلب
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = $_GET['order_id'];

// جلب تفاصيل الطلب من قاعدة البيانات
$query = "SELECT o.*, oi.product_id, oi.quantity, oi.price, p.name FROM orders o
          JOIN order_details oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          WHERE o.id = $order_id";

$result = $db->get(query: $query);


// عرض تفاصيل الطلب
$order = $result[0];
// var_dump($order[0]);
echo "<h2>Order Confirmation</h2>";
echo "<p>Order ID: " . $order['id'] . "</p>";
echo "<p>Total Price: $" . $order['total_price'] . "</p>";
echo "<p>Shipping Address: " . htmlspecialchars($order['shipping_address']) . "</p>";

echo "<h3>Order Items</h3>";
// print_r($result);
foreach ($result as $order) {

    echo "<p>Product: " . htmlspecialchars($order['name']) . " | Quantity: " . $order['quantity'] . " | Price: $" . $order['price'] . "</p>";
}
// while ($row = $result->fetch_assoc()) {
// }
?>