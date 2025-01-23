<?php
session_start();
require "../model/orm_v2.php";
// session_destroy();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();
if (!isset($_SESSION['user_id']) && empty($_SESSION['cart'])) {
    header("Location: login.php");
    exit;
}

// جلب العناصر من السلة
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$query = "SELECT c.*, p.name, p.price FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = $user_id";

$result = $db->get($query);

// حساب الإجمالي
$total_price = 0;
$cart_items = [];
foreach ($result as $row) {
    $total_price += $row['quantity'] * $row['price'];
    $cart_items[] = $row;
}

?>
<h2>Checkout</h2>
<form action="place_order.php" method="POST">
    <h3>Your Cart</h3>
    <?php foreach ($cart_items as $item): ?>
        <div class="cart-item">
            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
            <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
            <p>Quantity: <?php echo $item['quantity']; ?></p>
        </div>
    <?php endforeach; ?>

    <h3>Total: $<?php echo $total_price; ?></h3>

    <h3>Shipping Address</h3>
    <textarea name="shipping_address" required placeholder="Enter your shipping address"></textarea>

    <button type="submit">Place Order</button>
</form>