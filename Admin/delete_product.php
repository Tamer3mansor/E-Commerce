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


// حذف المنتج
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $db->delete('products', ["id" => $product_id]);
}

header("Location: index.php");
exit;
?>