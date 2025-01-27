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
require "../views/dist/order_details.html";

?>