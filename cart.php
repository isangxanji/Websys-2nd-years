<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Websys Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --bg: #f8f9fa;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--bg); color: #333; }

        /* Navbar */
                /* Navigation */ 
        body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  background: #f9f9f9;
  color: #333;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 15px 40px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.logo {
  font-size: 1.5em;
  font-weight: bold;
  color: #007bff;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 20px;
}

.nav-links a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
}

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }

        /* Left Side: Cart Items */
        .cart-wrapper { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { margin-bottom: 20px; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; }
        
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th { text-align: left; padding: 10px; color: #7f8c8d; font-size: 0.8rem; text-transform: uppercase; }
        .cart-table td { padding: 15px 10px; border-bottom: 1px solid #eee; }
        
        .item-info { display: flex; align-items: center; gap: 15px; }
        .item-info img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        
        .qty-input { width: 50px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; }
        .remove-btn { color: #e74c3c; cursor: pointer; background: none; border: none; }

        /* Right Side: Buying Form */
        .checkout-card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); height: fit-content; }
        .summary-line { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total-line { border-top: 2px solid #eee; margin-top: 15px; padding-top: 15px; font-weight: bold; font-size: 1.2rem; }

        .buying-form { margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }

        .btn-checkout { background: var(--success); color: white; width: 100%; padding: 15px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-checkout:hover { background: #219150; }

        @media (max-width: 850px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<!--
<nav>
    <div class="logo">WEBSYS SHOP</div>
    <ul class="nav-links">
        <li><a href="homepage.html">Home</a></li>
        <li><a href="shopsection.html">Shop</a></li>
        <li><a href="cartsection.html"><i class="fas fa-shopping-cart"></i> Cart</a></li>
    </ul>
</nav>-->

 <!-- Navigation -->
  <header class="navbar">
    <div class="logo">ShopEase</div>
    <nav>
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="shopsection.php">Shop</a></li>
        <li><a href="clothingsection.php">Clothing</a></li>
        <li><a href="electronicsection.php">Electronics</a></li>
        <!--<li><a href="shopsection.html">Sale</a></li>-->
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        <li><a href="MyAccount.php">My Account</a></li>
      </ul>
    </nav>
  </header>

<div class="container">
    <div class="cart-wrapper">
        <h2>Your Shopping Cart</h2>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="cartItems">
                <tr>
                    <td>
                        <div class="item-info">
                            <img src="laptop.jpg" alt="Laptop">
                            <div>
                                <strong>High-End Laptop</strong><br>
                                <small>Electronics Section</small>
                            </div>
                        </div>
                    </td>
                    <td><input type="number" value="1" min="1" class="qty-input"></td>
                    <td>₱45,000.00</td>
                    <td><button class="remove-btn"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                    <td>
                        <div class="item-info">
                            <img src="tshirt.png" alt="Tshirt">
                            <div>
                                <strong>Cotton T-Shirt</strong><br>
                                <small>Clothing Section</small>
                            </div>
                        </div>
                    </td>
                    <td><input type="number" value="2" min="1" class="qty-input"></td>
                    <td>₱900.00</td>
                    <td><button class="remove-btn"><i class="fas fa-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="checkout-card">
        <h2>Order Summary</h2>
        <div class="summary-line">
            <span>Subtotal</span>
            <span>₱45,900.00</span>
        </div>
        <div class="summary-line">
            <span>Shipping</span>
            <span>₱150.00</span>
        </div>
        <div class="summary-line total-line">
            <span>Total</span>
            <span style="color: var(--success);">₱46,050.00</span>
        </div>

        <form class="buying-form" id="buyingForm">
            <h3 style="font-size: 1rem; margin-bottom: 10px; margin-top: 20px;">Shipping Details</h3>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" placeholder="Recipient Name" required>
            </div>
            <div class="form-group">
                <label>Delivery Address</label>
                <input type="text" placeholder="House No, Street, Brgy" required>
            </div>
            
            <h3 style="font-size: 1rem; margin-bottom: 10px; margin-top: 20px;">Payment Method</h3>
            <div class="form-group">
                <select id="paymentMethod" required>
                    <option value="cod">Cash on Delivery (COD)</option>
                    <option value="gcash">GCash / Maya</option>
                    <option value="card">Credit/Debit Card</option>
                </select>
            </div>
            
            <button type="submit" class="btn-checkout">Place Order Now</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('buyingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const method = document.getElementById('paymentMethod').value;
        let message = "Thank you for your purchase! ";
        
        if(method === "cod") {
            message += "Please prepare the exact amount for your delivery.";
        } else {
            message += "Redirecting you to the secure payment gateway...";
        }

        alert(message);
        // In a real app, you would clear the cart and redirect to a 'Success' page
        window.location.href = "homepage.html";
    });

    // Simple remove logic for the UI
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('tr').remove();
            alert("Item removed from cart.");
        });
    });
</script>

</body>
</html>
