<?php
session_start();
include 'db.php';


// Safety fix: make sure seller/product tables and required columns exist before MyAccount.php uses them.
// This keeps the existing working pages unchanged, but prevents missing-table/column errors here.
function ensure_table_and_columns(mysqli $conn): void {
    $conn->query("CREATE TABLE IF NOT EXISTS sellers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        fullname VARCHAR(100) NOT NULL DEFAULT '',
        business_email VARCHAR(150) NOT NULL DEFAULT '',
        shop_name VARCHAR(150) NOT NULL DEFAULT '',
        warehouse_address VARCHAR(255) NOT NULL DEFAULT '',
        bank_name VARCHAR(100) NULL,
        account_number VARCHAR(100) NULL,
        shop_description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_business_email (business_email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        seller_id INT NULL,
        product_title VARCHAR(200) NOT NULL DEFAULT '',
        category VARCHAR(100) NOT NULL DEFAULT '',
        sku VARCHAR(100) NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        stock_level INT NOT NULL DEFAULT 0,
        product_description TEXT NULL,
        parcel_weight DECIMAL(10,2) NULL,
        variations VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // If a table already existed but was incomplete, add only the missing columns.
    $sellerColumns = [
        'user_id' => 'INT NULL',
        'fullname' => "VARCHAR(100) NOT NULL DEFAULT ''",
        'business_email' => "VARCHAR(150) NOT NULL DEFAULT ''",
        'shop_name' => "VARCHAR(150) NOT NULL DEFAULT ''",
        'warehouse_address' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'bank_name' => 'VARCHAR(100) NULL',
        'account_number' => 'VARCHAR(100) NULL',
        'shop_description' => 'TEXT NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ];

    $productColumns = [
        'seller_id' => 'INT NULL',
        'product_title' => "VARCHAR(200) NOT NULL DEFAULT ''",
        'category' => "VARCHAR(100) NOT NULL DEFAULT ''",
        'sku' => 'VARCHAR(100) NULL',
        'price' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
        'stock_level' => 'INT NOT NULL DEFAULT 0',
        'product_description' => 'TEXT NULL',
        'parcel_weight' => 'DECIMAL(10,2) NULL',
        'variations' => 'VARCHAR(255) NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ];

    foreach ($sellerColumns as $column => $definition) {
        $check = $conn->query("SHOW COLUMNS FROM sellers LIKE '$column'");
        if ($check && $check->num_rows === 0) {
            $conn->query("ALTER TABLE sellers ADD COLUMN $column $definition");
        }
    }

    foreach ($productColumns as $column => $definition) {
        $check = $conn->query("SHOW COLUMNS FROM products LIKE '$column'");
        if ($check && $check->num_rows === 0) {
            $conn->query("ALTER TABLE products ADD COLUMN $column $definition");
        }
    }
}

ensure_table_and_columns($conn);

$message = "";
$messageType = "";
$userId = $_SESSION['user_id'] ?? null;
$seller = null;

function clean_input($value) {
    return trim($value ?? '');
}

// Get current seller account if this logged-in user already registered as seller.
if ($userId) {
    $findSeller = $conn->prepare("SELECT * FROM sellers WHERE user_id = ? LIMIT 1");
    $findSeller->bind_param("i", $userId);
    $findSeller->execute();
    $sellerResult = $findSeller->get_result();
    if ($sellerResult->num_rows === 1) {
        $seller = $sellerResult->fetch_assoc();
    }
    $findSeller->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'seller_registration') {
        $fullname = clean_input($_POST['fullname']);
        $businessEmail = clean_input($_POST['business_email']);
        $shopName = clean_input($_POST['shop_name']);
        $warehouseAddress = clean_input($_POST['warehouse_address']);
        $bankName = clean_input($_POST['bank_name']);
        $accountNumber = clean_input($_POST['account_number']);
        $shopDescription = clean_input($_POST['shop_description']);

        if ($fullname === '' || $businessEmail === '' || $shopName === '' || $warehouseAddress === '') {
            $message = "Please fill out all required seller registration fields.";
            $messageType = "error";
        } elseif (!filter_var($businessEmail, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid business email.";
            $messageType = "error";
        } else {
            // If this business email already exists, update that seller record instead of inserting a duplicate.
            // This fixes the duplicate entry error for the unique business_email column.
            if (!$seller) {
                $findByEmail = $conn->prepare("SELECT * FROM sellers WHERE business_email = ? LIMIT 1");
                $findByEmail->bind_param("s", $businessEmail);
                $findByEmail->execute();
                $emailResult = $findByEmail->get_result();
                if ($emailResult->num_rows === 1) {
                    $seller = $emailResult->fetch_assoc();
                }
                $findByEmail->close();
            }

            // If seller already exists, update it. Otherwise, create it.
            if ($seller) {
                $stmt = $conn->prepare("UPDATE sellers SET user_id=?, fullname=?, business_email=?, shop_name=?, warehouse_address=?, bank_name=?, account_number=?, shop_description=? WHERE sellers_id=?");
                $stmt->bind_param("isssssssi", $userId, $fullname, $businessEmail, $shopName, $warehouseAddress, $bankName, $accountNumber, $shopDescription, $seller['sellers_id']);
            } else {
                $stmt = $conn->prepare("INSERT INTO sellers (user_id, fullname, business_email, shop_name, warehouse_address, bank_name, account_number, shop_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssss", $userId, $fullname, $businessEmail, $shopName, $warehouseAddress, $bankName, $accountNumber, $shopDescription);
            }

            if ($stmt->execute()) {
                $message = "Seller account saved successfully. You can now add products.";
                $messageType = "success";

                $sellerId = $seller ? $seller['sellers_id'] : $stmt->insert_id;
                $refresh = $conn->prepare("SELECT * FROM sellers WHERE sellers_id = ? LIMIT 1");
                $refresh->bind_param("i", $sellerId);
                $refresh->execute();
                $seller = $refresh->get_result()->fetch_assoc();
                $refresh->close();
            } else {
                $message = "Seller account was not saved. The business email may already be used.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }

    if ($formType === 'product_listing') {
        if (!$seller) {
            $message = "Please complete seller registration before adding a product.";
            $messageType = "error";
        } else {
            $productTitle = clean_input($_POST['product_title']);
            $category = clean_input($_POST['category']);
            $sku = clean_input($_POST['sku']);
            $price = clean_input($_POST['price']);
            $stockLevel = clean_input($_POST['stock_level']);
            $productDescription = clean_input($_POST['product_description']);
            $parcelWeight = clean_input($_POST['parcel_weight']);
            $variations = clean_input($_POST['variations']);

            if ($productTitle === '' || $category === '' || $price === '' || $stockLevel === '') {
                $message = "Please fill out all required product fields.";
                $messageType = "error";
            } else {
                $priceValue = (float)$price;
                $stockValue = (int)$stockLevel;
                $weightValue = ($parcelWeight === '') ? null : (float)$parcelWeight;
                $sellerId = (int)$seller['sellers_id'];

                $stmt = $conn->prepare("INSERT INTO products (seller_id, product_title, category, sku, price, stock_level, product_description, parcel_weight, variations) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssdisds", $sellerId, $productTitle, $category, $sku, $priceValue, $stockValue, $productDescription, $weightValue, $variations);

                if ($stmt->execute()) {
                    $message = "Product listed successfully and saved to the database.";
                    $messageType = "success";
                } else {
                    $message = "Product was not saved: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Center | Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary:#2c3e50; --accent:#3498db; --success:#27ae60; --bg:#f4f7f6; --white:#ffffff; --text:#333; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color:var(--bg); color:var(--text); }
        .navbar { display:flex; justify-content:space-between; align-items:center; background:white; padding:15px 40px; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
        .logo { font-size:1.5em; font-weight:bold; color:#007bff; }
        .nav-links { list-style:none; display:flex; gap:20px; }
        .nav-links a { text-decoration:none; color:#333; font-weight:500; }
        .container { max-width:900px; margin:40px auto; padding:0 20px; }
        .card { background:var(--white); padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); margin-bottom:30px; }
        h2 { margin-bottom:20px; color:var(--primary); border-bottom:2px solid #eee; padding-bottom:10px; display:flex; align-items:center; gap:10px; }
        .grid-form { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .full-width { grid-column:span 2; }
        .form-group { margin-bottom:15px; }
        label { display:block; font-weight:600; margin-bottom:5px; font-size:0.9rem; }
        input, select, textarea { width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; outline:none; }
        input:focus, select:focus, textarea:focus { border-color:var(--accent); }
        .required { color:#e74c3c; }
        .btn { padding:12px 25px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; transition:0.3s; }
        .btn-primary { background:var(--accent); color:white; width:100%; }
        .btn-success { background:var(--success); color:white; width:100%; }
        .btn:hover { opacity:0.9; transform:translateY(-1px); }
        .file-box { border:2px dashed #ddd; padding:20px; text-align:center; border-radius:8px; color:#666; }
        .message { margin-bottom:20px; padding:12px; border-radius:6px; text-align:center; }
        .success { background:#d4edda; color:#155724; }
        .error { background:#f8d7da; color:#721c24; }
        .note { background:#eaf4ff; color:#245070; padding:12px; border-radius:6px; margin-bottom:18px; }
        @media (max-width:700px) { .grid-form { grid-template-columns:1fr; } .full-width { grid-column:span 1; } .navbar { padding:15px; } .nav-links { gap:10px; font-size:0.9rem; } }
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

<div class="container">
    <?php if ($message !== ""): ?>
        <div class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section id="onboardingSection">
        <div class="card">
            <h2><i class="fas fa-id-card"></i> 1. Seller Registration</h2>
            <?php if (!$userId): ?>
                <div class="note">Tip: log in first so your seller account will be connected to your user account.</div>
            <?php endif; ?>
            <form method="POST" action="MyAccount.php">
                <input type="hidden" name="form_type" value="seller_registration">
                <div class="grid-form">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($seller['fullname'] ?? ($_SESSION['fullname'] ?? '')); ?>" placeholder="Juan Dela Cruz" required>
                    </div>
                    <div class="form-group">
                        <label>Business Email <span class="required">*</span></label>
                        <input type="email" name="business_email" value="<?php echo htmlspecialchars($seller['business_email'] ?? ($_SESSION['email'] ?? '')); ?>" placeholder="shop@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Shop Name <span class="required">*</span></label>
                        <input type="text" name="shop_name" value="<?php echo htmlspecialchars($seller['shop_name'] ?? ''); ?>" placeholder="e.g. Manila Tech Threads" required>
                    </div>
                    <div class="form-group">
                        <label>Warehouse Address <span class="required">*</span></label>
                        <input type="text" name="warehouse_address" value="<?php echo htmlspecialchars($seller['warehouse_address'] ?? ''); ?>" placeholder="City, Province" required>
                    </div>
                    <div class="form-group">
                        <label>Bank Name</label>
                        <select name="bank_name">
                            <?php $selectedBank = $seller['bank_name'] ?? ''; ?>
                            <option value="BDO" <?php if ($selectedBank === 'BDO') echo 'selected'; ?>>BDO</option>
                            <option value="BPI" <?php if ($selectedBank === 'BPI') echo 'selected'; ?>>BPI</option>
                            <option value="GCash / Maya" <?php if ($selectedBank === 'GCash / Maya') echo 'selected'; ?>>GCash / Maya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="account_number" value="<?php echo htmlspecialchars($seller['account_number'] ?? ''); ?>" placeholder="0000-0000-00">
                    </div>
                    <div class="form-group full-width">
                        <label>Shop Description</label>
                        <textarea name="shop_description" rows="3" placeholder="Tell buyers about your shop..."><?php echo htmlspecialchars($seller['shop_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Verification Document (BIR 2303 / ID)</label>
                        <div class="file-box"><i class="fas fa-cloud-upload-alt"></i> File upload display only for now. Seller details still save to database.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $seller ? 'Update Seller Account' : 'Complete Onboarding & Start Selling'; ?></button>
            </form>
        </div>
    </section>

    <section id="listingSection">
        <div class="card">
            <h2><i class="fas fa-plus-circle"></i> 2. Create New Listing</h2>
            <?php if (!$seller): ?>
                <div class="note">Complete seller registration first. After saving it, this product form will save listings under your seller account.</div>
            <?php endif; ?>
            <form method="POST" action="MyAccount.php">
                <input type="hidden" name="form_type" value="product_listing">
                <div class="grid-form">
                    <div class="form-group full-width">
                        <label>Product Title <span class="required">*</span></label>
                        <input type="text" name="product_title" placeholder="e.g. Brand New Gaming Laptop RTX 4060" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span class="required">*</span></label>
                        <select name="category" required>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>SKU (Stock Keeping Unit)</label>
                        <input type="text" name="sku" placeholder="AUTO-GEN-123">
                    </div>
                    <div class="form-group">
                        <label>Price (₱) <span class="required">*</span></label>
                        <input type="number" name="price" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Level <span class="required">*</span></label>
                        <input type="number" name="stock_level" min="0" placeholder="1" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Product Description</label>
                        <textarea name="product_description" rows="4" placeholder="Features, materials, specs..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Parcel Weight (kg)</label>
                        <input type="number" name="parcel_weight" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label>Variations</label>
                        <input type="text" name="variations" placeholder="e.g. S, M, L or Blue, Red">
                    </div>
                    <div class="form-group full-width">
                        <label>Product Photos</label>
                        <div class="file-box"><i class="fas fa-images"></i> Photo upload display only for now. Product text details still save to database.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Publish Product to Marketplace</button>
                <p style="text-align:center; margin-top:15px; font-size:0.8rem; color:#666;">By publishing, you agree to the Seller Terms and Conditions.</p>
            </form>
        </div>
    </section>
</div>
</body>
</html>
