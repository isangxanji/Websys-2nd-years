<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = "Please enter your email and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, fullname, email, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];

                header("Location: homepage.php");
                exit();
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "No account found with that email.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopEase - Login</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .message { margin-bottom: 15px; padding: 10px; border-radius: 5px; text-align: center; background: #f8d7da; color: #721c24; }
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
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        <li><a href="MyAccount.php">My Account</a></li>
      </ul>
    </nav>
  </header>

  <main class="login-container">
    <form class="login-box" method="POST" action="login.php">
      <h2>Welcome Back!</h2>

      <?php if ($message !== ""): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <label>Email Address</label>
      <input type="email" name="email" placeholder="Enter your email" required />

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required />

      <div class="remember">
        <input type="checkbox" id="remember" />
        <label for="remember">Remember Me</label>
      </div>

      <button type="submit">Log in</button>

      <div class="links">
        <a href="#">Forgot Password?</a>
        <a href="signin.php">Sign Up ></a>
      </div>
    </form>
  </main>
</body>
</html>
