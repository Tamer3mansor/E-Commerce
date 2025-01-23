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

// جلب المنتجات والطلبات
try {
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
    $products = $db->get($query);
    // category search 
    $from = $db->from("categories");
    $query = "";
    $query .= $select . $from;
    $categories_result = $db->get($query);
    //orders 
    $orders = $db->get('Select * from orders');
} catch (mysqli_sql_exception) {
    echo "Error: " . mysqli_error($db->create_connection());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Admin Dashboard</h1>
        <div class="mt-4">
            <h2>Products</h2>
            <a href="add_product.php" class="btn btn-success mb-3">Add New Product</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id']; ?></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td>$<?= number_format($product['price'], 2); ?></td>
                            <td>
                                <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_product.php?id=<?= $product['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    Categories
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"> <a href="index.php">
                            All Categories </a></li>
                    <?php foreach ($categories_result as $category) { ?>
                        <li class="list-group-item"> <a href="index.php?id=<?= $category['id'] ?>" }>
                                <?= $category['name'] ?> </a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="mt-5">
            <h2>Orders</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id']; ?></td>
                            <td><?= $order['user_id']; ?></td>
                            <td>$<?= number_format($order['total_price'], 2); ?></td>
                            <td><?= htmlspecialchars($order['status']); ?></td>
                            <td>
                                <a href="view_order.php?id=<?= $order['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="update_order_status.php?id=<?= $order['id']; ?>"
                                    class="btn btn-primary btn-sm">Update Status</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>