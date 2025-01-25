<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
function select_product()
{
    global $db;
    $select = $db->select('*');
    $from = $db->from('products');
    $order_by = $db->orderby("create desc");
    $limits = $db->limit('1');
    $query = "";
    if (isset($_REQUEST['product_id']) || isset($_REQUEST['id'])) { // if specified post 
        $product_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        $where = $db->Where("id = $product_id");
        $query .= $select . $from . $where;
    } else {
        $query .= $select . $from . $order_by . $limits;
    }
    $product_result = $db->get($query);
    return $product_result[0];
}
function select_comments()
{
    global $product_result;
    global $db;
    $product_id = $product_result['id'];
    $select = $db->select('content , created_at');
    $from = $db->from('comments');
    $where = $db->where("product_id = $product_id");
    $comments_result = $db->get($select . $from . $where);
    return $comments_result;

}
function select_user()
{
    global $db;
    global $post_result;
    $user_id = $post_result['user_id'];
    $select = $db->select('*');
    $from = $db->from('users');
    $where = $db->Where("user_id = $user_id", "");
    $query = "";
    $query .= $select . $from . $where;
    $user_result = $db->get($query);
    if (isset($user_result[0]))
        return $user_result[0];
    else
        return ['name' => "Unknown User"];
}
function select_Category()
{
    global $db;
    global $post_result;
    $post_id = $post_result['post_id'];
    $select = $db->select('*');
    $join = $db->join('category_has_posts , category', 'chp , c', 'inner join', 'chp.category_id = c.category_id');
    $where = $db->where("chp.post_id = $post_id");
    return $db->get($select . $join . $where);

}
function select_images()
{
    global $product_result;
    global $db;
    $product_id = $product_result['id'];
    $select = $db->select('image');
    $from = $db->from('images');
    $where = $db->where("product_id = $product_id ");
    return $db->get($select . $from . $where);
}

$product_result = select_product();
$comments = select_comments();
$images[] = select_images();
$images = $images[0];
// $user_result = select_user();
// $tags = select_category();
// if (isset(select_image()[0]['image_path']))
//     $image = select_image()[0]['image_path'];
// print_r($product_result);
require "../views/dist/products.html";
?>