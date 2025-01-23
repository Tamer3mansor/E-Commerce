<?php
session_start();
include '../model/orm_v2.php';
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
// تأكد من أن المستخدم Admin
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../login.php");
//     exit;
// }


// تحديث حالة الطلب
if (isset($_GET['id']) && isset($_GET['status'])) {
    $order_id = $_GET['id'];
    $status = $_GET['status'];
    $db->update("orders", ['status' => $status], ["id" => $order_id]);
}

header("Location: index.php");
exit;
?>