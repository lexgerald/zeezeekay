<?php
// checkout/place-order.php - Process order
require_once '../config/config.php';
require_once '../config/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
$paymentMethod = $_POST['payment_method'] ?? 'manual';
$reference = $_POST['reference'] ?? '';

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

// Check if this is an AJAX request (for Orange Money)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

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
        'type' => $paymentMethod,
        'order_id' => $orderId,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'reference' => $reference
    ])]);
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    // Commit transaction
    $db->commit();
    
    // If AJAX request (Orange Money), return JSON
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'message' => 'Order placed successfully'
        ]);
        exit;
    }
    
    // Regular form submission - redirect to success
    header("Location: " . BASE_URL . "checkout/order-success.php?order_id=" . $orderId);
    exit;
    
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();
    error_log("Order placement error: " . $e->getMessage());
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
    
    header("Location: " . BASE_URL . "checkout/checkout.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>