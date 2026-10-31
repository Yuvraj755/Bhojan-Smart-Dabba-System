
<?php
session_start();
require_once "config/database.php";

if (isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === "" || $email === "" || $phone === "" ||
        $address === "" || $password === "" || $confirmPassword === "") {

        $error = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {

        $error = "Please enter a valid phone number (10-15 digits).";

    } elseif (strlen($password) < 8) {

        $error = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirmPassword) {

        $error = "Passwords do not match.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM customers WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $error = "This email is already registered.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO customers
                (name, email, phone, password, address)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $phone,
                $hashedPassword,
                $address
            );

            if ($stmt->execute()) {

                $success = "Registration successful! Please login.";

            } else {

                $error = "Registration failed. Please try again.";

            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BHOJAN | Customer Registration</title>

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
            <a href="login.php" class="login-btn">Login</a>
        </div>
    </div>
</header>

<section class="auth-section">

    <div class="auth-container">

        <div class="auth-heading">
            <h1>Create Your Account</h1>
            <p>Join BHOJAN and enjoy delicious Indian thalis.</p>
        </div>

        <?php if ($error !== ""): ?>
            <div class="auth-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success !== ""): ?>
            <div class="auth-success">
                <?php echo htmlspecialchars($success); ?>
                <a href="login.php">Login Now</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                       placeholder="Enter your full name"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email"
                       placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone"
                       placeholder="Enter 10-digit phone number"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Delivery Address</label>
                <textarea name="address"
                          placeholder="Enter your complete address"
                          required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Minimum 8 characters"
                       required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"
                       placeholder="Confirm your password"
                       required>
            </div>

            <button type="submit" class="auth-submit">
                Create Account
            </button>

        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="login.php">Login Here</a>
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
