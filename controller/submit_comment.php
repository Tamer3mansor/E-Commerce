<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
$product_id = $_POST['product_id'];
$comment_content = $_POST['comment'];
function insert_into_comments()
{
    global $db;
    global $product_id;
    global $comment_content;
    echo ($product_id);
    echo ($comment_content);
    $insert_id = $db->insert('comments', ['product_id' => $product_id, 'content' => $comment_content]);
    return $insert_id;
}
function insert_into_post_comments()
{
    global $db;
    global $post_id;
    global $comment_id;
    $insert_id = $db->insert('post_comments', ['post_id' => $post_id, 'comment_id' => $comment_id]);
    return $insert_id;
}
$comment_id = insert_into_comments();
// $post_comment_id = insert_into_post_comments();
header("Location: product.php?id=" . $product_id);

?>