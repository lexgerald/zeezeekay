<?php
// checkout/success.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId > 0 && isset($_SESSION['user_id'])) {
    $db = getDB();
    
    // Update order status to paid if it's still pending
    $stmt = $db->prepare("UPDATE orders SET status = 'paid' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    
    // Get order details
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    // Update payment status
    if ($order) {
        $stmt = $db->prepare("UPDATE payments SET status = 'completed' WHERE order_id = ?");
        $stmt->execute([$orderId]);
    }
} else {
    $order = null;
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow">
                <div class="card-body py-5">
                    <div class="success-animation">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="mt-3">Payment Successful!</h2>
                    <p class="lead">Thank you for your order. Your payment has been processed successfully.</p>
                    
                    <?php if($order): ?>
                        <div class="alert alert-info text-start">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Order #:</strong> <?php echo $order['id']; ?><br>
                                    <strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Total:</strong> Le <?php echo number_format($order['total_amount'], 2); ?><br>
                                    <strong>Status:</strong> <span class="badge bg-success">Paid</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-secondary text-start">
                            <strong>Delivery Address:</strong><br>
                            <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                            <?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_zip']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <a href="<?php echo BASE_URL; ?>orders/orders.php" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> View My Orders
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-animation {
    animation: successPulse 1s ease;
}

@keyframes successPulse {
    0% { transform: scale(0.8); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<?php require_once '../includes/footer.php'; ?>