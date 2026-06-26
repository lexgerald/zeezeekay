<?php
// cart/add-to-cart.php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON

$response = ['success' => false, 'message' => ''];

try {
    // Get product ID and quantity
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    // Validate quantity
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if product exists in database
    require_once '../config/db.php';
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Check stock
    if ($product['stock'] < $quantity) {
        throw new Exception('Not enough stock available. Only ' . $product['stock'] . ' left.');
    }
    
    // Add to cart
    if (isset($_SESSION['cart'][$productId])) {
        // Check if new quantity exceeds stock
        $newQuantity = $_SESSION['cart'][$productId] + $quantity;
        if ($product['stock'] < $newQuantity) {
            throw new Exception('Not enough stock available. Only ' . $product['stock'] . ' left.');
        }
        $_SESSION['cart'][$productId] = $newQuantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
    
    // Calculate new cart count
    $cartCount = array_sum($_SESSION['cart']);
    
    $response['success'] = true;
    $response['message'] = 'Product added to cart successfully!';
    $response['cart_count'] = $cartCount;
    $response['product_name'] = $product['name'];
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>