
<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

$stmt = $conn->prepare(
    "SELECT id, total_items, grand_total, status, created_at
     FROM orders
     WHERE customer_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $customerId);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | BHOJAN</title>
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

<section class="order-history-section">
    <div class="order-history-container">

        <h1>My Orders</h1>
        <p class="order-history-subtitle">
            View and manage your BHOJAN orders.
        </p>

        <?php if ($orders->num_rows > 0): ?>

            <div class="order-table-wrapper">
                <table class="order-history-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo (int) $order['id']; ?></td>

                                <td>
                                    <?php
                                    echo date(
                                        "d M Y, h:i A",
                                        strtotime($order['created_at'])
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php echo (int) $order['total_items']; ?>
                                </td>

                                <td>
                                    ₹<?php echo number_format(
                                        (float) $order['grand_total'],
                                        2
                                    ); ?>
                                </td>

                                <td>
                                    <span class="order-status">
                                        <?php
                                        echo htmlspecialchars($order['status']);
                                        ?>
                                    </span>
                                </td>

                                <td>
                                    <a
                                        class="order-view-btn"
                                        href="order-details.php?id=<?php echo (int) $order['id']; ?>"
                                    >
                                        View Details
                                    </a>
                                    <a
                                        class="order-view-btn"
                                         href="track-order.php?id=<?php echo (int) $order['id']; ?>"
                                            >
                                                Track Order
                                            </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="no-orders-box">
                <h2>No Orders Yet!</h2>
                <p>You have not placed any orders yet.</p>
                <a href="menu.php" class="order-view-btn">
                    Explore Menu
                </a>
            </div>

        <?php endif; ?>

    </div>
</section>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
