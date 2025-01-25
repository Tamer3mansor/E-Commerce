<?php
session_start();
include '../model/orm_v2.php';
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
// تأكد من أن المستخدم Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);

    // تحميل الصورة


    try {
        $result = $db->insert('categories', ["name" => $name]);
        header("Location: index.php");
        exit;
    } catch (mysqli_sql_exception) {
        echo "Error: " . mysqli_errno($db->create_connection());
    }
}

// جلب التصنيفات

$categories = $db->get(query: "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Add New Category</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>


            <div class="mb-3">
                <label for="category" class="form-label"> Exist Category</label>
                <select name="category" id="category" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Add Category</button>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>