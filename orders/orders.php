<?php
// orders/orders.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$db = getDB();
$userId = $_SESSION['user_id'];

// Auto-delete pending orders older than 24 hours
function autoDeleteUserOldOrders($db, $userId) {
    $deletedCount = 0;
    try {
        // Get pending orders older than 24 hours for this user
        $stmt = $db->prepare("SELECT id FROM orders 
                               WHERE user_id = ? 
                               AND status = 'pending' 
                               AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute([$userId]);
        $oldOrders = $stmt->fetchAll();
        
        foreach($oldOrders as $order) {
            $orderId = $order['id'];
            
            $db->beginTransaction();
            
            // Delete order items
            $stmt = $db->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);
            
            // Delete payments
            $stmt = $db->prepare("DELETE FROM payments WHERE order_id = ?");
            $stmt->execute([$orderId]);
            
            // Delete the order
            $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            
            $db->commit();
            $deletedCount++;
        }
        
        return $deletedCount;
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Auto-delete error for user $userId: " . $e->getMessage());
        return 0;
    }
}

// Run auto-delete for this user
$deletedCount = autoDeleteUserOldOrders($db, $userId);

// Get orders with payment payload to retrieve phone number
$stmt = $db->prepare("SELECT o.*, p.payload as payment_payload 
                       FROM orders o 
                       LEFT JOIN payments p ON o.id = p.order_id 
                       WHERE o.user_id = ? 
                       ORDER BY o.created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

// Process orders to extract phone from payload
foreach($orders as &$order) {
    $order['phone'] = '';
    if (!empty($order['payment_payload'])) {
        $payload = json_decode($order['payment_payload'], true);
        $order['phone'] = $payload['customer_phone'] ?? '';
    }
}

// Calculate order age for each order
foreach($orders as &$order) {
    $orderDate = new DateTime($order['created_at']);
    $now = new DateTime();
    $diff = $now->diff($orderDate);
    $order['age_days'] = $diff->days;
    $order['age_hours'] = $diff->h;
    $order['is_old'] = $diff->days >= 1;
}
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-box-seam"></i> My Orders</h2>
            <p class="text-muted">View all your orders and their status</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
    
    <?php if($deletedCount > 0): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bi bi-clock-history"></i> 
            <?php echo $deletedCount; ?> pending order(s) older than 24 hours have been automatically removed.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if(empty($orders)): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-box-seam" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3">No Orders Yet</h4>
                <p class="text-muted">You haven't placed any orders yet. Start shopping now!</p>
                <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Start Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Age</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td><strong>Le <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    <td>
                                        <?php if($order['status'] === 'paid'): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Paid</span>
                                        <?php elseif($order['status'] === 'pending'): ?>
                                            <?php if($order['is_old']): ?>
                                                <span class="badge bg-danger"><i class="bi bi-clock"></i> Expired</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                                            <?php endif; ?>
                                        <?php elseif($order['status'] === 'completed'): ?>
                                            <span class="badge bg-info"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Failed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($order['is_old'] && $order['status'] === 'pending'): ?>
                                            <span class="badge bg-danger">Expired (<?php echo $order['age_days']; ?>d)</span>
                                        <?php elseif($order['status'] === 'pending'): ?>
                                            <span class="badge bg-secondary"><?php echo $order['age_hours']; ?>h old</span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($order['status'] === 'pending' && !$order['is_old']): ?>
                                            <a href="<?php echo BASE_URL; ?>orders/invoice.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-receipt"></i> View Invoice
                                            </a>
                                        <?php elseif($order['status'] === 'paid'): ?>
                                            <a href="<?php echo BASE_URL; ?>orders/invoice.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-success">
                                                <i class="bi bi-receipt"></i> View Invoice
                                            </a>
                                        <?php elseif($order['status'] === 'pending' && $order['is_old']): ?>
                                            <span class="text-muted">Order Expired</span>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>orders/invoice.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-secondary">
                                                <i class="bi bi-receipt"></i> View Invoice
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Order Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Total Orders</h6>
                                <h3 class="mb-0"><?php echo count($orders); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Pending</h6>
                                <h3 class="mb-0 text-warning">
                                    <?php 
                                        $pending = array_filter($orders, function($o) { 
                                            return $o['status'] === 'pending' && !$o['is_old']; 
                                        });
                                        echo count($pending);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Paid</h6>
                                <h3 class="mb-0 text-success">
                                    <?php 
                                        $paid = array_filter($orders, function($o) { 
                                            return $o['status'] === 'paid'; 
                                        });
                                        echo count($paid);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Expired</h6>
                                <h3 class="mb-0 text-danger">
                                    <?php 
                                        $expired = array_filter($orders, function($o) { 
                                            return $o['status'] === 'pending' && $o['is_old']; 
                                        });
                                        echo count($expired);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Note about order expiration -->
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Note:</strong> Pending orders are automatically removed after 24 hours if not paid.
                    Please complete your payment within 24 hours to avoid order cancellation.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.table th {
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    padding: 6px 12px;
}

.badge i {
    margin-right: 4px;
}

.card {
    border: none;
    border-radius: 12px;
}

.card-body {
    padding: 1.5rem;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 4px;
    margin: 0 2px;
}
</style>

<?php require_once '../includes/footer.php'; ?>