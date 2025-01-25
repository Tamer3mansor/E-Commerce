<?php
session_start();
include '../model/orm_v2.php';
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
// تأكد من أن المستخدم Admin
print_r($_GET);
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

print_r($_GET);

// تحديث حالة الطلب
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    $db->delete("orders", ["id" => $order_id]);
}
header("Location:index.php");
exit;
?>