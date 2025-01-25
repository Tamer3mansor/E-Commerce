<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
if (!$_SESSION['user_id']) {
    header('location:login.php');
    exit();
}
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = (int) $_GET['id'];
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $session_id = 0;
    } else {
        $user_id = 0;
        $session_id = session_id();
    }
    //check if have an cart
    $select = $db->select("*");
    $from = $db->from("cart");
    $where = $db->where("user_id = $user_id ");
    $result = $db->get($select . $from . $where);
    if ($result) {
        $select = $db->select("product_id,quantity");
        $from = $db->from("cart");
        $where = $db->where("product_id = $product_id AND user_id = $user_id    ");
        $cart_result = $db->get($select . $from . $where);
        // print_r($cart_result);
    }
    //


    if ($cart_result[0]['quantity'] > 0) {
        $row = $result[0];
        $new_quantity = $row['quantity'] + 1;  // زيادة الكمية بمقدار 1
        $row_id = $row['id'];
        $update_stmt = $db->update('cart', ["quantity" => $new_quantity], ["id" => $row_id]);
        ;
    } else {
        // إذا لم يكن المنتج موجود في السلة، نقوم بإضافته
        $db->insert('cart', ["product_id" => $product_id, "user_id" => $user_id, "session_id" => $session_id, "quantity" => 1]);

    }

    // إعادة توجيه المستخدم إلى صفحة السلة أو الصفحة الرئيسية بعد إضافة المنتج
    header("Location: cart.php");
    exit;
}
?>