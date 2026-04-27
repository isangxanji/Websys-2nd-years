<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  fullname = $_POST['fullname'];

  $sql = "INSERT INTO users (fullname) VALUES ('$fullname')";

  if ($conn=>query($sql) === TRUE) {
    echo " Record inserted successfully!";
  }
}
!>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopEase - Create Account</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body> 
  <header class="navbar">
    <div class="logo">ShopEase</div>
    <nav>
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="#">Shop</a></li>
        <li><a href="#">Clothing</a></li>
        <li><a href="#">Electronics</a></li>
        <li><a href="MyAccount.php">My Account</a></li>
        <!--<li><a href="shopsection.php">Sale</a></li>-->
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        <li><a href="login.php">Log In</a></li>
      </ul>
    </nav>
  </header>

  <main class="signup-container">
    <form class="signup-box"  method="POST"> /*action ="homepage.php"*/

      <h2>Create Your Account</h2>

      <label>Full Name</label>
      <input type="text" name="fullname" placeholder="Enter your full name" required />

      <label>Email Address</label>
      <input type="email" name="email" placeholder="Enter your email" required />

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required />

      <label>Confirm Password</label>
      <input type="password" placeholder="Confirm your password" required />

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