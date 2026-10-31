
<?php
session_start();

require_once "config/database.php";

// Check customer login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Thali information
$thalis = [
    1 => ["name" => "Special Veg Thali", "price" => 149],
    2 => ["name" => "Punjabi Thali", "price" => 179],
    3 => ["name" => "Gujarati Thali", "price" => 159],
    4 => ["name" => "South Indian Thali", "price" => 139],
    5 => ["name" => "Chicken Thali", "price" => 199],
    6 => ["name" => "Egg Thali", "price" => 169]
];

if (empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit;
}

// Fetch customer details
$customerId = (int) $_SESSION['customer_id'];

$stmt = $conn->prepare(
    "SELECT name, email, phone, address
     FROM customers WHERE id = ?"
);

$stmt->bind_param("i", $customerId);
$stmt->execute();

$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$customer) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit;
}

// Calculate cart amount using server-side prices
$subtotal = 0;
$totalItems = 0;
$cartItems = [];

foreach ($_SESSION['cart'] as $id => $quantity) {

    $id = (int) $id;
    $quantity = (int) $quantity;

    if (!isset($thalis[$id]) || $quantity <= 0) {
        continue;
    }

    $item = $thalis[$id];
    $itemTotal = $item['price'] * $quantity;

    $subtotal += $itemTotal;
    $totalItems += $quantity;

    $cartItems[] = [
        "name" => $item['name'],
        "price" => $item['price'],
        "quantity" => $quantity,
        "total" => $itemTotal
    ];
}

if (empty($cartItems)) {
    header("Location: menu.php");
    exit;
}

$deliveryCharge = 30;
$grandTotal = $subtotal + $deliveryCharge;

$error = "";

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Place order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {

        $error = "Invalid request. Please refresh the page.";

    } elseif ($address === "") {

        $error = "Please enter your delivery address.";

    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {

        $error = "Please enter a valid phone number.";

    } else {

        try {

            $conn->begin_transaction();

            // Save order
            $orderStmt = $conn->prepare(
                "INSERT INTO orders
                (customer_id, total_items, subtotal,
                 delivery_charge, grand_total,
                 delivery_address, payment_method)
                VALUES (?, ?, ?, ?, ?, ?, 'Cash on Delivery')"
            );

            $orderStmt->bind_param(
                "iiddds",
                $customerId,
                $totalItems,
                $subtotal,
                $deliveryCharge,
                $grandTotal,
                $address
            );

            $orderStmt->execute();

            $orderId = $conn->insert_id;

            $orderStmt->close();

            // Save individual order items
            $itemStmt = $conn->prepare(
                "INSERT INTO order_items
                (order_id, thali_name, price, quantity, item_total)
                VALUES (?, ?, ?, ?, ?)"
            );

            foreach ($cartItems as $item) {

                $name = $item['name'];
                $price = $item['price'];
                $quantity = $item['quantity'];
                $itemTotal = $item['total'];

                $itemStmt->bind_param(
                    "isdis",
                    $orderId,
                    $name,
                    $price,
                    $quantity,
                    $itemTotal
                );

                $itemStmt->execute();
            }

            $itemStmt->close();

            // Update customer contact details
            $updateStmt = $conn->prepare(
                "UPDATE customers
                 SET phone = ?, address = ?
                 WHERE id = ?"
            );

            $updateStmt->bind_param(
                "ssi",
                $phone,
                $address,
                $customerId
            );

            $updateStmt->execute();
            $updateStmt->close();

            // Commit database transaction
            $conn->commit();

            // Clear cart after successful order
            $_SESSION['cart'] = [];
            unset($_SESSION['csrf_token']);

            header("Location: order-success.php?id=" . $orderId);
            exit;

        } catch (Throwable $e) {

            $conn->rollback();

            error_log($e->getMessage());

            $error = "Unable to place your order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BHOJAN | Checkout</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<header>
    <div class="navbar">

        <div class="logo">BHOJAN<span>.</span></div>

        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
        </nav>

        <div class="nav-buttons">
            <span>
                Hello, <?php echo htmlspecialchars($customer['name']); ?>
            </span>
            <a href="logout.php" class="register-btn">Logout</a>
        </div>

    </div>
</header>

<section class="checkout-section">

    <div class="checkout-heading">
        <h1>Complete Your Order</h1>
        <p>Just one more step to enjoy your favourite thali!</p>
    </div>

    <?php if ($error !== ""): ?>

        <div class="auth-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <div class="checkout-layout">

        <div class="checkout-form-container">

            <h2>Delivery Information</h2>

            <form method="POST" action="checkout.php">

                <input type="hidden"
                       name="csrf_token"
                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-group">

                    <label>Customer Name</label>

                    <input type="text"
                           value="<?php echo htmlspecialchars($customer['name']); ?>"
                           readonly>

                </div>

                <div class="form-group">

                    <label>Email Address</label>

                    <input type="email"
                           value="<?php echo htmlspecialchars($customer['email']); ?>"
                           readonly>

                </div>

                <div class="form-group">

                    <label>Mobile Number</label>

                    <input type="tel"
                           name="phone"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? $customer['phone']); ?>"
                           required>

                </div>

                <div class="form-group">

                    <label>Delivery Address</label>

                    <textarea name="address"
                              placeholder="Enter your complete delivery address"
                              required><?php echo htmlspecialchars($_POST['address'] ?? $customer['address']); ?></textarea>

                </div>

                <div class="payment-box">

                    <h3>Payment Method</h3>

                    <label>
                        <input type="radio"
                               checked
                               disabled>
                        Cash on Delivery
                    </label>

                    <p>Pay when your order is delivered.</p>

                </div>

                <button type="submit" class="auth-submit">
                    Place Order - ₹<?php echo number_format($grandTotal, 2); ?>
                </button>

            </form>

        </div>

        <div class="checkout-summary">

            <h2>Your Order</h2>

            <?php foreach ($cartItems as $item): ?>

                <div class="summary-row">

                    <span>
                        <?php echo htmlspecialchars($item['name']); ?>
                        × <?php echo $item['quantity']; ?>
                    </span>

                    <strong>
                        ₹<?php echo number_format($item['total'], 2); ?>
                    </strong>

                </div>

            <?php endforeach; ?>

            <hr>

            <div class="summary-row">
                <span>Total Items</span>
                <strong><?php echo $totalItems; ?></strong>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <strong>₹<?php echo number_format($subtotal, 2); ?></strong>
            </div>

            <div class="summary-row">
                <span>Delivery Charge</span>
                <strong>₹<?php echo number_format($deliveryCharge, 2); ?></strong>
            </div>

            <div class="summary-total">

                <span>Grand Total</span>

                <span>₹<?php echo number_format($grandTotal, 2); ?></span>

            </div>

        </div>

    </div>

</section>

<footer>
    <h2>BHOJAN.</h2>
    <p>Delicious Indian Thali, Delivered to Your Doorstep.</p>
    <p>© 2026 BHOJAN. All Rights Reserved.</p>
</footer>

</body>
</html>
