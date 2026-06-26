<?php
// checkout/failed.php
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : 'Payment processing failed. Please try again.';

// If we have an order ID, update its status to failed
if ($orderId > 0 && isset($_SESSION['user_id'])) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE orders SET status = 'failed' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    
    $stmt = $db->prepare("UPDATE payments SET status = 'failed' WHERE order_id = ?");
    $stmt->execute([$orderId]);
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow">
                <div class="card-body py-5">
                    <div class="failed-animation">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="mt-3">Payment Failed</h2>
                    <p class="lead">We're sorry, but your payment could not be processed.</p>
                    
                    <div class="alert alert-danger text-start">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?php echo $error; ?>
                    </div>
                    
                    <div class="mt-4">
                        <?php if($orderId > 0): ?>
                            <a href="<?php echo BASE_URL; ?>checkout/checkout.php?retry=<?php echo $orderId; ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-arrow-repeat"></i> Try Again
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>cart/cart.php" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Return to Cart
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.failed-animation {
    animation: failedShake 0.5s ease;
}

@keyframes failedShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}
</style>

<?php require_once '../includes/footer.php'; ?>