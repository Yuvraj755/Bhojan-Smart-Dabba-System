
<?php
session_start();

$thalis = [
    [
        "id" => 1,
        "name" => "Special Veg Thali",
        "category" => "veg",
        "price" => 149,
        "description" => "Dal, Rice, 2 Roti, Paneer Sabzi, Salad and Pickle",
        "image" => "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=600"
    ],
    [
        "id" => 2,
        "name" => "Punjabi Thali",
        "category" => "veg",
        "price" => 179,
        "description" => "Dal Makhani, Shahi Paneer, Roti, Rice and Salad",
        "image" => "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600"
    ],
    [
        "id" => 3,
        "name" => "Gujarati Thali",
        "category" => "veg",
        "price" => 159,
        "description" => "Gujarati Kadhi, Roti, Rice, Dhokla and Sabzi",
        "image" => "https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=600"
    ],
    [
        "id" => 4,
        "name" => "South Indian Thali",
        "category" => "veg",
        "price" => 139,
        "description" => "Rice, Sambar, Rasam, Curd, Papad and Vegetables",
        "image" => "https://images.unsplash.com/photo-1630383249896-424e482df921?w=600"
    ],
    [
        "id" => 5,
        "name" => "Chicken Thali",
        "category" => "nonveg",
        "price" => 199,
        "description" => "Chicken Curry, Rice, Roti, Salad and Pickle",
        "image" => "https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=600"
    ],
    [
        "id" => 6,
        "name" => "Egg Thali",
        "category" => "nonveg",
        "price" => 169,
        "description" => "Egg Curry, Rice, Roti, Salad and Pickle",
        "image" => "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600"
    ]
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {

    $itemId = (int) $_POST['item_id'];

    $validItem = false;

    foreach ($thalis as $thali) {
        if ($thali['id'] === $itemId) {
            $validItem = true;
            break;
        }
    }

    if ($validItem) {
        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]++;
        } else {
            $_SESSION['cart'][$itemId] = 1;
        }
    }

    header("Location: menu.php?added=1");
    exit;
}

$cartCount = array_sum($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BHOJAN | Our Thali Menu</title>

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
        </nav>

        <div class="nav-buttons">
            <a href="login.php" class="login-btn">Login</a>
            <a href="register.php" class="register-btn">Sign Up</a>
            <a href="cart.php" class="cart-link">
                🛒 Cart (<?php echo $cartCount; ?>)
            </a>
        </div>

    </div>
</header>

<section class="menu-banner">
    <h1>Explore Our <span>Delicious Thalis</span></h1>
    <p>Fresh, tasty and authentic Indian food delivered to your doorstep.</p>
</section>

<section class="menu-section">

    <div class="menu-heading">
        <h2>Our Food Menu</h2>
        <p>Choose your favourite thali</p>
    </div>

    <?php if (isset($_GET['added'])): ?>
        <div class="success-message">
            Thali successfully added to your cart!
        </div>
    <?php endif; ?>

    <div class="menu-controls">

        <input
            type="text"
            id="searchInput"
            placeholder="Search your favourite thali..."
        >

        <div class="category-buttons">
            <button class="filter-btn active" data-category="all">All</button>
            <button class="filter-btn" data-category="veg">Veg</button>
            <button class="filter-btn" data-category="nonveg">Non-Veg</button>
        </div>

    </div>

    <div class="menu-container">

        <?php foreach ($thalis as $thali): ?>

            <div class="menu-card"
                 data-category="<?php echo $thali['category']; ?>"
                 data-name="<?php echo htmlspecialchars(strtolower($thali['name'])); ?>">

                <img
                    src="<?php echo htmlspecialchars($thali['image']); ?>"
                    alt="<?php echo htmlspecialchars($thali['name']); ?>"
                >

                <div class="menu-card-content">

                    <span class="food-category">
                        <?php echo $thali['category'] === 'veg' ? '🟢 Veg' : '🔴 Non-Veg'; ?>
                    </span>

                    <h3><?php echo htmlspecialchars($thali['name']); ?></h3>

                    <p><?php echo htmlspecialchars($thali['description']); ?></p>

                    <div class="menu-card-bottom">

                        <h4>₹<?php echo $thali['price']; ?></h4>

                        <form method="POST" action="menu.php">
                            <input
                                type="hidden"
                                name="item_id"
                                value="<?php echo $thali['id']; ?>"
                            >

                            <button type="submit" name="add_to_cart">
                                Add +
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <p id="noResults" class="no-results">
        No thali found. Try another search.
    </p>

</section>

<footer>
    <h2>BHOJAN.</h2>
    <p>Delicious Indian Thali, Delivered to Your Doorstep.</p>
    <p>© 2026 BHOJAN. All Rights Reserved.</p>
</footer>

<script src="assets/js/script.js"></script>

</body>
</html>
