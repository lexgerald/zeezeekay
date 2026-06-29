<?php
// includes/navbar.php
// Make sure BASE_URL is defined
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    // Get cart count from session or database
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $cartCount = array_sum(array_column($cartItems, 'quantity'));
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>index.php">
            <i class="bi bi-bag-fill" style="color: #f3b919;"></i> Zeekay
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>faq.php">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>products/index.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>cart/cart.php">
                        <i class="bi bi-cart3"></i> Cart
                        <span class="badge bg-warning text-dark" id="cartCount"><?php echo $cartCount; ?></span>
                    </a>
                </li>
                <?php if(isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <?php endif; ?>
                <?php if(isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>orders/orders.php">
                        <i class="bi bi-speedometer2"></i> My Orders
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="navbar-text me-3 text-light">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                    <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-warning btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>