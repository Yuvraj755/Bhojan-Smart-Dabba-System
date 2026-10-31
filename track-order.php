
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

$stmt = $conn->prepare(
    "SELECT id, status, created_at, total_items, grand_total,
            delivery_address
     FROM orders
     WHERE id = ? AND customer_id = ?"
);

$stmt->bind_param("ii", $orderId, $customerId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: orders.php");
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

$statusStages = [
    "Pending" => [
        "step" => 1,
        "label" => "Order Placed",
        "message" => "Your order has been received successfully."
    ],
    "Confirmed" => [
        "step" => 2,
        "label" => "Order Confirmed",
        "message" => "The restaurant has confirmed your order."
    ],
    "Preparing" => [
        "step" => 3,
        "label" => "Preparing Food",
        "message" => "Your delicious thali is being prepared."
    ],
    "Out for Delivery" => [
        "step" => 4,
        "label" => "Out for Delivery",
        "message" => "Your order is on its way to your address."
    ],
    "Delivered" => [
        "step" => 5,
        "label" => "Delivered",
        "message" => "Your order has been delivered. Enjoy your meal!"
    ]
];

$currentStatus = $order['status'];

if (isset($statusStages[$currentStatus])) {
    $currentStep = $statusStages[$currentStatus]['step'];
    $statusLabel = $statusStages[$currentStatus]['label'];
    $statusMessage = $statusStages[$currentStatus]['message'];
} else {
    $currentStep = 0;
    $statusLabel = $currentStatus;
    $statusMessage = "Your order status is being updated.";
}

$steps = [
    "Order Placed",
    "Order Confirmed",
    "Preparing Food",
    "Out for Delivery",
    "Delivered"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order | BHOJAN</title>
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

<section class="tracking-section">
    <div class="tracking-container">

        <a href="orders.php" class="back-to-orders">
            &larr; Back to My Orders
        </a>

        <h1>Track Your Order</h1>
        <p class="tracking-subtitle">
            Follow your BHOJAN order from preparation to delivery.
        </p>

        <div class="tracking-order-card">

            <div class="tracking-order-heading">
                <div>
                    <h2>Order #<?php echo (int) $order['id']; ?></h2>
                    <p>
                        Placed on:
                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime($order['created_at'])
                        );
                        ?>
                    </p>
                </div>

                <span class="tracking-status">
                    <?php echo htmlspecialchars($statusLabel); ?>
                </span>
            </div>

            <div class="tracking-message">
                <?php echo htmlspecialchars($statusMessage); ?>
            </div>

            <div class="tracking-progress">
                <?php foreach ($steps as $index => $step): ?>
                    <?php $stepNumber = $index + 1; ?>

                    <div class="tracking-step
                        <?php
                        echo ($currentStep > 0 && $stepNumber <= $currentStep)
                            ? 'completed'
                            : '';
                        ?>">

                        <div class="tracking-circle">
                            <?php
                            echo ($currentStep > 0 && $stepNumber <= $currentStep)
                                ? '&#10003;'
                                : $stepNumber;
                            ?>
                        </div>

                        <p><?php echo htmlspecialchars($step); ?></p>
                    </div>

                    <?php if ($stepNumber < 5): ?>
                        <div class="tracking-line
                            <?php
                            echo ($currentStep > 0 && $stepNumber < $currentStep)
                                ? 'completed'
                                : '';
                            ?>">
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>

            <div class="tracking-order-info">
                <h3>Order Information</h3>

                <p>
                    <strong>Total Items:</strong>
                    <?php echo (int) $order['total_items']; ?>
                </p>

                <p>
                    <strong>Grand Total:</strong>
                    ₹<?php echo number_format(
                        (float) $order['grand_total'],
                        2
                    ); ?>
                </p>

                <p>
                    <strong>Delivery Address:</strong>
                    <?php
                    echo nl2br(htmlspecialchars($order['delivery_address']));
                    ?>
                </p>
            </div>

            <p class="tracking-note">
                Order progress is displayed using the latest status saved in
                the system. Refresh this page to check for updates.
            </p>

        </div>
    </div>
</section>

</body>
</html>
