
<?php
session_start();
require_once "config/database.php";

if (isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === "" || $password === "") {

        $error = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password
             FROM customers
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $customer = $result->fetch_assoc();

            if (password_verify($password, $customer['password'])) {

                session_regenerate_id(true);

                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_name'] = $customer['name'];
                $_SESSION['customer_email'] = $customer['email'];

                header("Location: index.php");
                exit;

            } else {

                $error = "Invalid email or password.";

            }

        } else {

            $error = "Invalid email or password.";

        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BHOJAN | Customer Login</title>

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
            <a href="orders.php">My Orders</a>
        </nav>

        <div class="nav-buttons">
            <a href="register.php" class="register-btn">Sign Up</a>
        </div>

    </div>
</header>

<section class="auth-section">

    <div class="auth-container">

        <div class="auth-heading">
            <h1>Welcome Back!</h1>
            <p>Login to your BHOJAN customer account.</p>
        </div>

        <?php if ($error !== ""): ?>
            <div class="auth-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="form-group">
                <label>Email Address</label>

                <input type="email" name="email"
                       placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Password</label>

                <input type="password" name="password"
                       placeholder="Enter your password"
                       required>
            </div>

            <button type="submit" class="auth-submit">
                Login
            </button>

        </form>

        <p class="auth-switch">
            Don't have an account?
            <a href="register.php">Register Here</a>
        </p>

    </div>

</section>

<footer>
    <h2>BHOJAN.</h2>
    <p>Delicious Indian Thali, Delivered to Your Doorstep.</p>
    <p>© 2026 BHOJAN. All Rights Reserved.</p>
</footer>

</body>
</html>
