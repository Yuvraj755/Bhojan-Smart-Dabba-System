
<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];
$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$orderId || $orderId <= 0) {
    header("Location: orders.php");
    exit;
}

$orderStmt = $conn->prepare(
    "SELECT id, total_items, subtotal, delivery_charge,
            grand_total, delivery_address, payment_method,
            status, created_at
     FROM orders
     WHERE id = ? AND customer_id = ?"
);

$orderStmt->bind_param("ii", $orderId, $customerId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

if ($orderResult->num_rows === 0) {
    $orderStmt->close();
    header("Location: orders.php");
    exit;
}

$order = $orderResult->fetch_assoc();
$orderStmt->close();

$itemStmt = $conn->prepare(
    "SELECT thali_name, price, quantity, item_total
     FROM order_items
     WHERE order_id = ?"
);

$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$items = $itemStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | BHOJAN</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="order-history-header">
    <div class="order-history-nav">
        <a href="index.php" class="order-history-logo">BHOJAN</a>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">My Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</header>

<section class="order-details-section">
    <div class="order-details-container">

        <a href="orders.php" class="back-to-orders">
            &larr; Back to My Orders
        </a>

        <h1>Order Details</h1>

        <div class="order-info-card">
            <div>
                <strong>Order ID:</strong>
                #<?php echo (int) $order['id']; ?>
            </div>

            <div>
                <strong>Order Date:</strong>
                <?php
                echo date(
                    "d M Y, h:i A",
                    strtotime($order['created_at'])
                );
                ?>
            </div>

            <div>
                <strong>Order Status:</strong>
                <span class="order-status">
                    <?php echo htmlspecialchars($order['status']); ?>
                </span>
            </div>

            <div>
                <strong>Payment Method:</strong>
                <?php echo htmlspecialchars($order['payment_method']); ?>
            </div>

            <div>
                <strong>Delivery Address:</strong>
                <?php
                echo nl2br(htmlspecialchars($order['delivery_address']));
                ?>
            </div>
        </div>

        <h2>Ordered Items</h2>

        <div class="order-table-wrapper">
            <table class="order-history-table">
                <thead>
                    <tr>
                        <th>Thali Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Item Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($item['thali_name']); ?>
                            </td>

                            <td>
                                ₹<?php echo number_format(
                                    (float) $item['price'],
                                    2
                                ); ?>
                            </td>

                            <td>
                                <?php echo (int) $item['quantity']; ?>
                            </td>

                            <td>
                                ₹<?php echo number_format(
                                    (float) $item['item_total'],
                                    2
                                ); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="order-total-card">
            <p>
                Subtotal:
                <strong>₹<?php echo number_format((float) $order['subtotal'], 2); ?></strong>
            </p>

            <p>
                Delivery Charge:
                <strong>₹<?php echo number_format((float) $order['delivery_charge'], 2); ?></strong>
            </p>

            <h2>
                Grand Total:
                ₹<?php echo number_format((float) $order['grand_total'], 2); ?>
            </h2>
        </div>

    </div>
</section>

</body>
</html>

<?php
$itemStmt->close();
$conn->close();
?>
