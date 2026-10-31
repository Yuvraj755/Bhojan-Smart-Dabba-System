
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHOJAN | Online Thali Delivery</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="navbar">
        <div class="logo">BHOJAN<span></span></div>

        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
            <a href="orders.php">My Orders</a>
        </nav>

        <<div class="nav-buttons">

    <?php if (isset($_SESSION['customer_id'])): ?>

        <span>
            Hello, <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
        </span>

        <a href="logout.php" class="register-btn">Logout</a>

    <?php else: ?>

        <a href="login.php" class="login-btn">Login</a>
        <a href="register.php" class="register-btn">Sign Up</a>

    <?php endif; ?>

</div>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <p class="tagline">FRESH • TASTY • HOMEMADE</p>

        <h1>Enjoy Delicious<br>
            <span>Indian Thali</span>
        </h1>

        <p class="description">
            Experience the authentic taste of India.
            Freshly prepared thalis delivered directly
            to your doorstep.
        </p>

        <a href="menu.php" class="explore-btn">
            Explore Thalis →
        </a>
    </div>

    <div class="hero-image">
        <img src="https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=900"
             alt="Indian Thali">
    </div>
</section>

<section class="categories">
    <h2>Explore Our <span>Thalis</span></h2>
    <p class="section-desc">Choose your favourite meal</p>

    <div class="food-container">

        <div class="food-card">
            <img src="https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500" alt="Veg Thali">
            <h3>Special Veg Thali</h3>
            <p>Dal, Rice, Roti, Sabzi and Salad</p>
            <h4>₹149</h4>
            <a href="menu.php">Order Now</a>
        </div>

        <div class="food-card">
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500" alt="Healthy Thali">
            <h3>Healthy Thali</h3>
            <p>Fresh vegetables and nutritious food</p>
            <h4>₹129</h4>
            <a href="menu.php">Order Now</a>
        </div>

        <div class="food-card">
            <img src="https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500" alt="Indian Meal">
            <h3>Premium Thali</h3>
            <p>Delicious traditional Indian dishes</p>
            <h4>₹199</h4>
            <a href="menu.php">Order Now</a>
        </div>

    </div>
</section>

<section class="about" id="about">
    <h2>Why Choose <span>BHOJAN?</span></h2>

    <div class="features">
        <div>
            <h3>🍛 Fresh Food</h3>
            <p>Freshly prepared delicious meals.</p>
        </div>

        <div>
            <h3>🚚 Fast Delivery</h3>
            <p>Your favourite thali at your doorstep.</p>
        </div>

        <div>
            <h3>💰 Affordable Price</h3>
            <p>Quality food at reasonable prices.</p>
        </div>
    </div>
</section>

<footer id="contact">
    <h2>BHOJAN.</h2>
    <p>Delicious Indian Thali, Delivered to Your Doorstep.</p>
    <p>© 2026 BHOJAN. All Rights Reserved.</p>
</footer>

</body>
</html>
