<?php
require "../model/orm_v2.php";
session_start();
$db = new orm(['localhost', 'root', '', 'blog']);
$db->create_connection();

function validate($email, $psw)
{
    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email';
    }
    if (empty($psw)) {
        $errors['psw'] = 'Password Required';
    }

    return $errors;
}
function select_users($email)
{
    global $db;
    $select = $db->SELECT('*');
    $from = $db->from('users');
    $where = $db->where("email = '$email' ");
    $result = $db->get($select . $from . $where);
    return @$result[0];
}
function set_session($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $email = $_POST['email'];
    $psw = $_POST['psw'];
    $errors = validate($email, $psw);
    if (empty($errors)) {
        $result = select_users($email);
        if ($result) {
            $db_psw = $result['password'];
            $db_type = $result['type'];
            if (password_verify($psw, $db_psw)) {
                set_session($result);
                header('Location:index.php');
            } else {
                echo $email;
                $errors['login'] = "Incorrect email or password.";
            }
        } else {
            echo $psw, $db_psw;
            $errors['login'] = "Incorrect email or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Login</h2>
        <?php if ($errors): ?>
            <div class="alert alert-danger"><?= $errors['login']; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="psw" class="form-label">Password</label>
                <input type="password" name="psw" id="psw" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>

</html>