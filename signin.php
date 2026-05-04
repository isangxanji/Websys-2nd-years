<?php
session_start();
include 'db.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($fullname === '' || $email === '' || $password === '' || $confirm_password === '') {
        $message = "Please fill out all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "This email is already registered. Please log in instead.";
            $messageType = "error";
        } else {
            // Save hashed password, not plain text password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashedPassword);

            if ($stmt->execute()) {
                $message = "Account created successfully! You can now log in.";
                $messageType = "success";
            } else {
                $message = "Registration failed: " . $stmt->error;
                $messageType = "error";
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopEase - Create Account</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .message { margin-bottom: 15px; padding: 10px; border-radius: 5px; text-align: center; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
  </style>
</head>
<body> 
  <header class="navbar">
    <div class="logo">ShopEase</div>
    <nav>
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="shopsection.php">Shop</a></li>
        <li><a href="clothingsection.php">Clothing</a></li>
        <li><a href="electronicsection.php">Electronics</a></li>
        <li><a href="MyAccount.php">My Account</a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        <li><a href="login.php">Log In</a></li>
      </ul>
    </nav>
  </header>

  <main class="signup-container">
    <form class="signup-box" method="POST" action="signin.php">
      <h2>Create Your Account</h2>

      <?php if ($message !== ""): ?>
        <div class="message <?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <label>Full Name</label>
      <input type="text" name="fullname" placeholder="Enter your full name" required />

      <label>Email Address</label>
      <input type="email" name="email" placeholder="Enter your email" required />

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required />

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" placeholder="Confirm your password" required />

      <div class="remember">
        <input type="checkbox" id="remember" />
        <label for="remember">Remember Me</label>
      </div>

      <button type="submit">Register</button>

      <p class="terms">
        By signing up, you agree to our <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a>.
      </p>

      <p class="login-link">
        Already have an account? <a href="login.php">Log in ›</a>
      </p>
    </form>
  </main> 
</body>
</html>
