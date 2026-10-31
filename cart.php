
<?php
session_start();

$thalis = [
    1 => [
        "name" => "Special Veg Thali",
        "price" => 149,
        "image" => "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=300"
    ],
    2 => [
        "name" => "Punjabi Thali",
        "price" => 179,
        "image" => "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300"
    ],
    3 => [
        "name" => "Gujarati Thali",
        "price" => 159,
        "image" => "https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=300"
    ],
    4 => [
        "name" => "South Indian Thali",
        "price" => 139,
        "image" => "https://images.unsplash.com/photo-1630383249896-424e482df921?w=300"
    ],
    5 => [
        "name" => "Chicken Thali",
        "price" => 199,
        "image" => "https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=300"
    ],
    6 => [
        "name" => "Egg Thali",
        "price" => 169,
        "image" => "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300"
    ]
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $itemId = (int) ($_POST['item_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (isset($thalis[$itemId]) && isset($_SESSION['cart'][$itemId])) {

        if ($action === 'increase') {
            $_SESSION['cart'][$itemId]++;
        }

        if ($action === 'decrease') {
            $_SESSION['cart'][$itemId]--;

            if ($_SESSION['cart'][$itemId] <= 0) {
                unset($_SESSION['cart'][$itemId]);
            }
        }

        if ($action === 'remove') {
            unset($_SESSION['cart'][$itemId]);
        }
    }

    header("Location: cart.php");
    exit;
}

$subtotal = 0;
$deliveryCharge = 30;
$totalItems = array_sum($_SESSION['cart']);

foreach ($_SESSION['cart'] as $id => $quantity) {
    if (isset($thalis[$id])) {
        $subtotal += $thalis[$id]['price'] * $quantity;
    }
}

$delivery = $subtotal > 0 ? $deliveryCharge : 0;
$grandTotal = $subtotal + $delivery;
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BHOJAN | Shopping Cart</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<header>
    <div class="navbar">

        <div class="logo">BHOJAN<span>.</span></div>

        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="index.php#about">About</a>
            <a href="index.php#contact">Contact</a>
            <a href="orders.php">My Orders</a>
        </nav>

        <div class="nav-buttons">
            <a href="menu.php" class="login-btn">Continue Shopping</a>
        </div>

    </div>
</header>

<section class="cart-section">

    <div class="cart-heading">
        <h1>My Shopping Cart 🛒</h1>
        <p>Review your delicious thali selection.</p>
    </div>

    <div class="cart-layout">

        <div class="cart-items">

            <?php if (empty($_SESSION['cart'])): ?>

                <div class="empty-cart">
                    <h2>Your Cart is Empty!</h2>
                    <p>You haven't added any thali yet.</p>

                    <a href="menu.php" class="explore-btn">
                        Explore Thalis
                    </a>
                </div>

            <?php else: ?>

                <?php foreach ($_SESSION['cart'] as $id => $quantity): ?>

                    <?php if (!isset($thalis[$id])) continue; ?>

                    <?php
                        $item = $thalis[$id];
                        $itemTotal = $item['price'] * $quantity;
                    ?>

                    <div class="cart-item">

                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                             alt="<?php echo htmlspecialchars($item['name']); ?>">

                        <div class="cart-item-info">

                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>

                            <p>Price: ₹<?php echo $item['price']; ?></p>

                            <h4>₹<?php echo $itemTotal; ?></h4>

                        </div>

                        <div class="cart-actions">

                            <form method="POST">

                                <input type="hidden" name="item_id"
                                       value="<?php echo $id; ?>">

                                <button name="action" value="decrease">−</button>

                                <span><?php echo $quantity; ?></span>

                                <button name="action" value="increase">+</button>

                            </form>

                            <form method="POST">

                                <input type="hidden" name="item_id"
                                       value="<?php echo $id; ?>">

                                <button name="action" value="remove"
                                        class="remove-btn">
                                    Remove
                                </button>

                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <?php if (!empty($_SESSION['cart'])): ?>

            <div class="cart-summary">

                <h2>Order Summary</h2>

                <div class="summary-row">
                    <span>Total Items</span>
                    <span><?php echo $totalItems; ?></span>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo $subtotal; ?></span>
                </div>

                <div class="summary-row">
                    <span>Delivery Charge</span>
                    <span>₹<?php echo $delivery; ?></span>
                </div>

                <div class="summary-total">
                    <span>Grand Total</span>
                    <span>₹<?php echo $grandTotal; ?></span>
                </div>

                <a href="checkout.php" class="checkout-btn">
                    Proceed to Checkout →
                </a>

                <a href="menu.php" class="continue-link">
                    Continue Shopping
                </a>

            </div>

        <?php endif; ?>

    </div>

</section>

<footer>
    <h2>BHOJAN.</h2>
    <p>Delicious Indian Thali, Delivered to Your Doorstep.</p>
    <p>© 2026 BHOJAN. All Rights Reserved.</p>
</footer>

</body>
</html>
