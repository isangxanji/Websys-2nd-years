<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopEase - Electronics</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
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

  <!-- Banner -->
  <section class="banner">
    <h1>Latest Electronics</h1>
    <p>Discover the best in tech and gadgets</p>
    <button>Shop Now</button>
  </section>

  <!-- Sorting and Categories -->
  <section class="sorting">
    <p>Showing 1-8 of 20 Products</p>
    <div class="sort-options">
      <label>Sort By:</label>
      <select>
        <option>Top Rated</option>
        <option>Price: Low to High</option>
        <option>Price: High to Low</option>
      </select>
      <label>View:</label>
      <select>
        <option>Grid</option>
        <option>List</option>
      </select>
    </div>
    <div class="category-buttons">
      <button>All</button>
      <button>Cameras</button>
      <button>Headphones</button>
      <button>Laptops</button>
      <button>Sale</button>
    </div>
  </section>

  <!-- Product Grid -->
  <section class="product-grid">
    <div class="product-card">
      <h3>Digital Camera</h3>
      <img src="camera.jpg" />
      <p>$299.00</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>Wireless Headphones</h3>
      <img src="headphones.avif" />
      <p>$79.00</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>Smartwatch</h3>
      <img src="watch.jpg" />
      <p>$149.99</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>Laptop</h3>
      <img src="laptop.jpg" />
      <p>$799.00</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>Portable Speaker</h3>
      <img src="speakers.webp" />
      <p><span class="old-price">$199.00</span> $119.25 (25% OFF)</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>Smartphone</h3>
      <img src="smartphone.webp" />
      <p>$499.00</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>DSLR Lens</h3>
      <img src="lens.jpg" />
      <p>$199.00</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
    <div class="product-card">
      <h3>Gaming Console</h3>
      <img src="console.webp" />
      <p>$349.00</p>
      <button>Add to Cart</button>
      <button>View Details</button>
    </div>
  </section>

  <!-- Promo Banner -->
  <section class="promo-banner">
    <h2>Save $100 on 4K Smart TVs</h2>
    <p>Upgrade your entertainment experience</p>
    <button>Shop Now</button>
  </section>
</body>
</html>