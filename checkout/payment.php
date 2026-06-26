<?php
// checkout/payment.php
require_once '../config/config.php';
require_once '../config/db.php';

session_start();

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
$zip = trim($_POST['zip'] ?? '');

// Validate inputs
if ($total <= 0) {
    header("Location: " . BASE_URL . "checkout/checkout.php?error=Invalid total amount");
    exit;
}

if (empty($address) || empty($city) || empty($zip)) {
    header("Location: " . BASE_URL . "checkout/checkout.php?error=Please fill in all shipping fields");
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
    $stmt->execute([$userId, $total, $address, $city, $zip]);
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
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    // Commit transaction
    $db->commit();
    
    // Generate a unique transaction ID
    $transactionId = 'ZK-' . $orderId . '-' . time() . '-' . uniqid();
    
    // Save transaction ID to order
    $stmt = $db->prepare("UPDATE orders SET transaction_id = ? WHERE id = ?");
    $stmt->execute([$transactionId, $orderId]);
    
    // Save payment record
    $stmt = $db->prepare("INSERT INTO payments (order_id, transaction_id, status, payload) VALUES (?, ?, 'pending', ?)");
    $stmt->execute([$orderId, $transactionId, json_encode(['order_id' => $orderId, 'amount' => $total])]);
    
    // ================================================
    // MONIME DIRECT PAYMENT LINK
    // Replace this URL with your actual Monime payment link
    // ================================================
    $monimePaymentUrl = 'https://pay.monime.io/023361815';
    
    // If you have a dynamic payment link with parameters:
    // $monimePaymentUrl = 'https://payment.monime.io/pay/your-payment-link?amount=' . $total . '&order_id=' . $orderId . '&transaction_id=' . $transactionId;
    
    // For testing without Monime (will redirect to success page)
    // $monimePaymentUrl = BASE_URL . 'checkout/success.php?order_id=' . $orderId;
    
    // Redirect to Monime payment page
    header("Location: " . $monimePaymentUrl);
    exit;
    
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();
    error_log("Payment processing error: " . $e->getMessage());
    header("Location: " . BASE_URL . "checkout/failed.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>