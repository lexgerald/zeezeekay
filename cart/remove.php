<?php
// cart/remove.php
session_start();

// Get the product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
    // Remove the product from cart
    unset($_SESSION['cart'][$productId]);
    
    // Optional: Add a success message
    $_SESSION['cart_message'] = 'Item removed from cart successfully!';
} else {
    $_SESSION['cart_error'] = 'Item not found in cart.';
}

// Redirect back to cart page
header("Location: cart.php");
exit;
?>