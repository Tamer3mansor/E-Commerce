<?php
require "../model/orm_v2.php";
session_start();
$errors = [];
function validate($name, $email, $psw, $psw_rep)
{
    global $db;
    $errors = [];
    if (empty($name)) {
        $errors['name'] = 'Enter Name';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email';
    }
    if (strlen($psw) < 10) {
        $errors['psw'] = 'Password length must be more than 10 characters';
    }
    if ($psw !== $psw_rep) {
        $errors['psw_rep'] = 'Passwords do not match';
    }
    $select = $db->select('email');
    $from = $db->from('users');
    $where = $db->where("email = '$email' ");
    $email_exit = $db->get($select . $from . $where);
    if ($email_exit) {
        $errors['email'] = 'This email used before';
    }
    return $errors;
}

$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $psw = $_POST['psw'];
    $psw_rep = $_POST['psw-repeat'];
    $errors = validate($name, $email, $psw, $psw_rep);
    print_r($errors);
    $hash = password_hash($psw, PASSWORD_DEFAULT, ['cost']);
    if (empty($errors)) {
        $result = $db->insert('users', ["username" => $name, "email" => $email, "password" => $hash, "role" => 'customer']);
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['role'] = 'customer';
        if ($result == 'Duplicate email')
            $errors['email'] = 'This email used before';
        if ($result) {
            header('location:index.php');
        }

    }
}





require "../views/dist/signup.html";

