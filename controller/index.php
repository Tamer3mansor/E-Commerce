<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
//search 
if (!empty($_REQUEST['search'])) {
    $search = $_REQUEST['search'];
    $select = $db->select("*");
    $from = $db->from("posts");
    $where = $db->Where("", "title like '%$search'");
    $order_by = $db->orderby('created_at desc');
    $query = "";
    $query .= $select . $from . $where . $order_by;
    $posts_result = $db->get($query);

}
//without search 
else
    if (isset($_REQUEST['id'])) {
        $category_id = $_REQUEST['id'];
        $where = $db->Where("category_id = $category_id");
    }

$select = $db->select("*");
$from = $db->from("products");
$order_by = $db->orderby('created_at desc');
$limits = $db->limit(10);
$query = "";
if (isset($_REQUEST['id'])) {
    $category_id = $_REQUEST['id'];
    $where = $db->Where("category_id = $category_id");
    $query .= $select . $from . $where . $order_by . $limits;
} else {
    $query .= $select . $from . $order_by . $limits;
}
$products_result = $db->get($query);
// category search 
$from = $db->from("categories");
$query = "";
$query .= $select . $from;
$categories_result = $db->get($query);
//images 

require "../views/dist/index.html";

?>