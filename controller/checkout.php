<?php
session_start();
require "../model/orm_v2.php";

// session_destroy();
$db = new orm(['localhost', 'root', '', 'commerce']);
$db->create_connection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get cart items for the logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT c.*, p.name, p.price FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = $user_id";

$result = $db->get($query);

// Calculate total price
$total_price = 0;
$cart_items = [];
foreach ($result as $row) {
    $total_price += $row['quantity'] * $row['price'];
    $cart_items[] = $row;
}

?>



<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    <title>Checkout</title>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="index.php">E-Commerce</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto py-4 py-lg-0">
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="cart.php">MyCart</a></li>
                    <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="orders.php">My Orders</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="logout.php">LogOut</a></li>
                        <li class="nav-link px-lg-3 py-3 py-lg-4"> <?= ($_SESSION['username']); ?></li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="login.php">Login</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Checkout</h2>

        <form action="place_order.php" method="POST">
            <h3>Your Cart</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h3>Total: $<?php echo number_format($total_price, 2); ?></h3>

        </form>
    </div>
    <div class"container">
        <div class="py-5 text-center">
            <h1>Checkout Form</h1>
        </div>
    </div>

    <div class="container">

        <form action="place_order.php" method="post">


            <div class="col-md-8 order-1">
                <h4 class="mb-3">Billing Address</h4>

                <div class="mb-4">
                    <label for="Address">Address</label>
                    <input type="text" required name="Address" class="form-control" placeholder="1234 Main St"
                        aria-label="Address">
                </div>

                <div class="mb-4">
                    <label for="Address2">Address 2 (optional)</label>
                    <input type="text" name="Address2" class="form-control" placeholder="Appartment or suite"
                        aria-label="Address2">
                </div>

                <div class="row">
                    <div class="col">
                        <label for="country">Government</label>
                        <select class="form-select" required>
                            <option selected>Choose...</option>
                            <option value="cairo">Cairo</option>
                            <option value="Giza">Giza</option>
                            <option value="Alex">Alex</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="state">State</label>
                        <select class="form-select" required>
                            <option selected>Choose...</option>
                            <option value="1">shibin_elkom</option>
                            <option value="2">.....</option>
                            <option value="3">........</option>
                        </select>
                    </div>
                    <div class="col mb-4">
                        <label for="zip" required>Zip Code</label>
                        <input type="text" class="form-control" name="zip" aria-label="zip">
                    </div>

                    <hr class="mb-4">



                    <hr class="mb-4">

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1"
                            checked>
                        <label class="form-check-label" for="flexRadioDefault1">
                            Credit card (Default)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                        <label class="form-check-label" for="flexRadioDefault2">
                            Debit card
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault3">
                        <label class="form-check-label" for="flexRadioDefault3">
                            Paypal
                        </label>
                    </div>

                    <div class="row">
                        <div class="col mb-4">
                            <label for="Card1">
                                Name on card
                            </label>
                            <input type="text" name="card_name" class="form-control" aria-label="card1">
                            <small class="text-muted">
                                Full name, as displayed on the card
                            </small>
                        </div>

                        <div class="col mb-4">
                            <label for="Card2">
                                Credit card Number
                            </label>
                            <input type="text" name="card_number" class="form-control" placeholder"1234-5678-9012"
                                aria-label="Card2">
                        </div>
                    </div>

                    <<div class="row">
                        <div class="col mb-3">
                            <label for="Card3">
                                Expiry Date
                            </label>
                            <input type="text" class="form-control" aria-label="card3">
                        </div>

                        <div class="col mb-3">
                            <label for="Card4">
                                CVV
                            </label>
                            <input type="text" class="form-control" aria-label="Card4">
                        </div>
                </div>

            </div>

            <hr class="mb-4">
            <?php if ($result) { ?>
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-success btn-lg">Place Order</button>
                </div>
            <?php } else { ?>
                <div class="mt-4 text-center">
                    <p>You do not have any thing to order yet got to
                        shopping </p>
                    <a class="btn btn-success btn-lg" href="index.php">Click To Shopping</a>
                </div>
            <?php } ?>
        </form>

    </div>
    </div>
    </div>


    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                

</body>

</html>