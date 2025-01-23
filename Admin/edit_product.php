<?php
session_start();
include '../model/orm_v2.php';
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $product = $db->get("SELECT * FROM products WHERE id = $product_id");
    $product = $product[0];

    if (!$product) {
        echo "Product not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $price = htmlspecialchars($_POST['price']);
    $description = htmlspecialchars($_POST['description']);
    $category_id = $_POST['category'];

    if (!empty($_FILES['image']['name'])) {

        $db->update('products', ["name" => $name, "price" => $price, "description" => $description, "category_id" => $category_id], ["id" => $product_id]);
        $db->delete('images', ["product_id" => $product_id]);
        foreach ($_FILES['image']['name'] as $key => $value) {
            $image = $_FILES['image']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'][$key], $target_file);
            $db->insert('images', ["product_id" => $product_id, "image" => $target_file]);
        }
    } else {
        $db->update('products', ["name" => $name, "price" => $price, "description" => $description, "category_id" => $category_id], ["id" => $product_id]);
    }

    header("Location: index.php");
    exit;
}

// جلب التصنيفات

$categories = $db->get("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Edit Product</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" id="name" class="form-control"
                    value="<?= htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01"
                    value="<?= htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"
                    required><?= htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id']; ?>" <?= ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image (optional)</label>
                <input type="file" name="image[]" id="image" class="form-control" multiple>
                <p>Current Image: <?= htmlspecialchars($product['image']); ?></p>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>