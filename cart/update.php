<?php
// cart/update.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
        if ($quantity > 0) {
            // Update quantity
            $_SESSION['cart'][$productId] = $quantity;
            $_SESSION['cart_message'] = 'Cart updated successfully!';
        } else {
            // Remove if quantity is 0 or less
            unset($_SESSION['cart'][$productId]);
            $_SESSION['cart_message'] = 'Item removed from cart!';
        }
    } else {
        $_SESSION['cart_error'] = 'Invalid product.';
    }
}

// Redirect back to cart page
header("Location: cart.php");
exit;
?>