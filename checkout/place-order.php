<?php
// checkout/place-order.php - Process order and show success
require_once '../config/config.php';
require_once '../config/db.php';

// No need to start session again - it's already started in config/config.php

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "checkout/checkout.php");
    exit;
}

$db = getDB();
$userId = $_SESSION['user_id'];
$total = floatval($_POST['total'] ?? 0);
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$phone = trim($_POST['zip'] ?? '');

// Validate inputs
if ($total <= 0) {
    header("Location: " . BASE_URL . "checkout/checkout.php?error=Invalid total amount");
    exit;
}

if (empty($address) || empty($city) || empty($phone)) {
    header("Location: " . BASE_URL . "checkout/checkout.php?error=Please fill in all required fields");
    exit;
}

// Validate phone number
if (!preg_match('/^[0-9]{8,15}$/', $phone)) {
    header("Location: " . BASE_URL . "checkout/checkout.php?error=Please enter a valid phone number");
    exit;
}

// Get cart items
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    header("Location: " . BASE_URL . "cart/cart.php");
    exit;
}

try {
    // Begin transaction
    $db->beginTransaction();
    
    // Create order
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, shipping_city, shipping_zip) 
                           VALUES (?, ?, 'pending', ?, ?, ?)");
    $stmt->execute([$userId, $total, $address, $city, $phone]);
    $orderId = $db->lastInsertId();
    
    // Add order items
    foreach($cart as $productId => $quantity) {
        $stmt = $db->prepare("SELECT price, stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Check stock
            if ($product['stock'] < $quantity) {
                throw new Exception("Not enough stock for product ID: $productId");
            }
            
            // Add order item
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $productId, $quantity, $product['price']]);
            
            // Update stock
            $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$quantity, $productId]);
        }
    }
    
    // Generate transaction ID
    $transactionId = 'ORDER-' . $orderId . '-' . time();
    $stmt = $db->prepare("UPDATE orders SET transaction_id = ? WHERE id = ?");
    $stmt->execute([$transactionId, $orderId]);
    
    // Save payment record
    $stmt = $db->prepare("INSERT INTO payments (order_id, transaction_id, status, payload) VALUES (?, ?, 'pending', ?)");
    $stmt->execute([$orderId, $transactionId, json_encode([
        'type' => 'manual',
        'order_id' => $orderId,
        'phone' => $phone,
        'address' => $address,
        'city' => $city
    ])]);
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    // Commit transaction
    $db->commit();
    
    // Show success page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Placed - Zeekay Store</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/zee_logo.png" type="image/x-icon">
        <style>
            body {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 20px;
            }
            .success-container {
                background: white;
                border-radius: 20px;
                padding: 50px;
                text-align: center;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 500px;
                width: 100%;
                animation: slideUp 0.6s ease;
            }
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .success-icon {
                font-size: 5rem;
                color: #28a745;
                animation: pulse 1.5s ease-in-out infinite;
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            .order-number {
                background: #f8f9fa;
                padding: 10px 20px;
                border-radius: 10px;
                display: inline-block;
                margin: 10px 0;
                font-size: 1.1rem;
            }
            .order-number strong {
                color: #333;
            }
            .btn-home {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                border: none;
                padding: 12px 40px;
                border-radius: 50px;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
                font-weight: 600;
            }
            .btn-home:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
                color: white;
            }
            .btn-orders {
                color: #667eea;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }
            .btn-orders:hover {
                color: #764ba2;
                text-decoration: underline;
            }
            .alert-info {
                background: #e7f3ff;
                border-color: #b6d4fe;
                color: #084298;
                border-radius: 10px;
            }
            .alert-info i {
                font-size: 1.2rem;
                margin-right: 8px;
            }
            @media (max-width: 480px) {
                .success-container {
                    padding: 30px 20px;
                }
                .success-icon {
                    font-size: 4rem;
                }
                h2 {
                    font-size: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="success-container">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2 class="mt-3 fw-bold text-success">Order Placed Successfully!</h2>
            <p class="text-muted">Thank you for your order. We will contact you shortly.</p>
            
            <div class="order-number">
                <strong>Order #:</strong> <?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="bi bi-clock-history"></i> 
                We will contact you within 24 hours to confirm your order.
            </div>
            
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>index.php" class="btn-home">
                    <i class="bi bi-house"></i> Return to Home
                </a>
            </div>
            
            <div class="mt-3">
                <a href="<?php echo BASE_URL; ?>orders/orders.php" class="btn-orders">
                    <i class="bi bi-box-seam"></i> View My Orders
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
    
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();
    error_log("Order placement error: " . $e->getMessage());
    header("Location: " . BASE_URL . "checkout/checkout.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>