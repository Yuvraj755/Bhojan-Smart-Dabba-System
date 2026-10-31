
<?php
session_start();

require_once "config/database.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$orderId || $orderId <= 0) {
    header("Location: orders.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

$stmt = $conn->prepare(
    "SELECT id, total_items, grand_total,
            payment_method, status, created_at
     FROM orders
     WHERE id = ? AND customer_id = ?"
);

$stmt->bind_param("ii", $orderId, $customerId);
$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    http_response_code(404);
    exit("Order not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BHOJAN | Order Successful</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<header>
    <div class="navbar">
        <div class="logo">BHOJAN<span>.</span></div>

        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="orders.php">My Orders</a>
        </nav>

        <div class="nav-buttons">
            <a href="logout.php" class="register-btn">Logout</a>
        </div>
    </div>
</header>

<section class="order-success-section">

    <div class="order-success-card">

        <div class="success-icon">✓</div>

        <h1>Order Placed Successfully!</h1>

        <p>
            Thank you for ordering from BHOJAN.
            Your delicious thali is on its way to being prepared.
        </p>

        <div class="order-details">

            <div>
                <span>Order ID</span>
                <strong>#<?php echo $order['id']; ?></strong>
            </div>

            <div>
                <span>Total Items</span>
                <strong><?php echo $order['total_items']; ?></strong>
            </div>

            <div>
                <span>Payment Method</span>
                <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong>
            </div>

            <div>
                <span>Order Status</span>
                <strong><?php echo htmlspecialchars($order['status']); ?></strong>
            </div>

            <div>
                <span>Order Amount</span>
                <strong class="order-amount">
                    ₹<?php echo number_format((float)$order['grand_total'], 2); ?>
                </strong>
            </div>

        </div>

        <a href="menu.php" class="explore-btn">
            Order More Thalis
        </a>

    </div>

</section>

<footer>
    <h2>BHOJAN.</h2>
    <p>Delicious Indian Thali, Delivered to Your Doorstep.</p>
    <p>© 2026 BHOJAN. All Rights Reserved.</p>
</footer>

</body>
</html>
