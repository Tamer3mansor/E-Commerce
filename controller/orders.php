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
if (!$_SESSION['user_id'] || ($_SESSION['role'] != 'customer')) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب تفاصيل الطلب من قاعدة البيانات
$query = "SELECT * from orders
          WHERE user_id = $user_id Order by created_at ";

$result = $db->get(query: $query);

if ($result) {

    $order = $result[0];
}




require "../views/dist/orders.html";

?>