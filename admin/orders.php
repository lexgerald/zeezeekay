<?php
// admin/orders.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'You do not have permission to access the admin area.';
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$db = getDB();
$message = '';
$error = '';

// Auto-delete orders older than 24 hours (pending status only)
function autoDeleteOldOrders($db) {
    $deletedCount = 0;
    try {
        $stmt = $db->prepare("SELECT id FROM orders 
                               WHERE status = 'pending' 
                               AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute();
        $oldOrders = $stmt->fetchAll();
        
        foreach($oldOrders as $order) {
            $orderId = $order['id'];
            
            $db->beginTransaction();
            
            // Delete order items first (due to foreign key constraints)
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
        
        if ($deletedCount > 0) {
            return "Auto-deleted $deletedCount pending order(s) older than 24 hours.";
        }
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Auto-delete error: " . $e->getMessage());
    }
    return null;
}

// Run auto-delete on page load
$autoDeleteMessage = autoDeleteOldOrders($db);
if ($autoDeleteMessage) {
    $message = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-clock-history"></i> ' . $autoDeleteMessage . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
}

// Handle manual delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $orderId = (int)$_POST['order_id'];
    
    if ($orderId > 0) {
        $stmt = $db->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if ($order) {
            if ($order['status'] === 'pending') {
                try {
                    $db->beginTransaction();
                    
                    $stmt = $db->prepare("DELETE FROM order_items WHERE order_id = ?");
                    $stmt->execute([$orderId]);
                    
                    $stmt = $db->prepare("DELETE FROM payments WHERE order_id = ?");
                    $stmt->execute([$orderId]);
                    
                    $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
                    $stmt->execute([$orderId]);
                    
                    $db->commit();
                    
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill"></i> Order #' . $orderId . ' deleted successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                } catch (Exception $e) {
                    $db->rollBack();
                    $error = '<div class="alert alert-danger">Error deleting order: ' . $e->getMessage() . '</div>';
                }
            } else {
                $error = '<div class="alert alert-warning">Only pending orders can be deleted.</div>';
            }
        } else {
            $error = '<div class="alert alert-danger">Order not found.</div>';
        }
    }
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'] ?? '';
    $validStatuses = ['pending', 'paid', 'failed'];
    
    if ($orderId > 0 && in_array($status, $validStatuses)) {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> Order status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
    }
}

// Handle search
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchField = isset($_GET['search_field']) ? $_GET['search_field'] : 'all';

// Build the WHERE clause for search
$whereClause = '';
$params = [];

if (!empty($searchQuery)) {
    if ($searchField === 'order_id') {
        $whereClause = "WHERE o.id = ?";
        $params[] = (int)$searchQuery;
    } elseif ($searchField === 'customer') {
        $whereClause = "WHERE u.name LIKE ?";
        $params[] = "%$searchQuery%";
    } else {
        // Search in both
        $whereClause = "WHERE o.id = ? OR u.name LIKE ?";
        $params[] = (int)$searchQuery;
        $params[] = "%$searchQuery%";
    }
}

// Get single order view
$viewOrderId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewOrder = null;

if ($viewOrderId > 0) {
    $stmt = $db->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.id = ?");
    $stmt->execute([$viewOrderId]);
    $viewOrder = $stmt->fetch();
    
    if ($viewOrder) {
        $stmt = $db->prepare("SELECT oi.*, p.name as product_name 
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.id 
                               WHERE oi.order_id = ?");
        $stmt->execute([$viewOrderId]);
        $orderItems = $stmt->fetchAll();
    }
}

// Get all orders with search
if (!empty($whereClause)) {
    $query = "SELECT o.*, u.name as user_name 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              $whereClause 
              ORDER BY o.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
} else {
    $orders = $db->query("SELECT o.*, u.name as user_name 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           ORDER BY o.created_at DESC")->fetchAll();
}

// Get order statistics
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$paidOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
$failedOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'failed'")->fetchColumn();

// Calculate orders older than 24 hours
$oldPendingOrders = $db->query("SELECT COUNT(*) FROM orders 
                                 WHERE status = 'pending' 
                                 AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetchColumn();
?>
<div class="container mt-4">
    <!-- Admin Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-box-seam"></i> Manage Orders</h2>
            <p class="text-muted">View and manage all customer orders</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Orders</h6>
                    <h2 class="mb-0"><?php echo $totalOrders; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Pending</h6>
                    <h2 class="mb-0"><?php echo $pendingOrders; ?></h2>
                    <?php if($oldPendingOrders > 0): ?>
                        <small class="text-danger"><?php echo $oldPendingOrders; ?> older than 24hrs</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Paid</h6>
                    <h2 class="mb-0"><?php echo $paidOrders; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Failed</h6>
                    <h2 class="mb-0"><?php echo $failedOrders; ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <?php echo $message; ?>
    <?php echo $error; ?>
    
    <!-- Auto-delete info -->
    <?php if($oldPendingOrders > 0): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bi bi-info-circle-fill"></i>
            <strong><?php echo $oldPendingOrders; ?> pending order(s)</strong> are older than 24 hours and will be automatically deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>admin/orders.php" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by Order ID or Customer Name..." 
                               value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="search_field" class="form-select">
                        <option value="all" <?php echo $searchField === 'all' ? 'selected' : ''; ?>>All Fields</option>
                        <option value="order_id" <?php echo $searchField === 'order_id' ? 'selected' : ''; ?>>Order ID</option>
                        <option value="customer" <?php echo $searchField === 'customer' ? 'selected' : ''; ?>>Customer Name</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?php echo BASE_URL; ?>admin/orders.php" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
            <?php if(!empty($searchQuery)): ?>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Found <strong><?php echo count($orders); ?></strong> result(s) for "<?php echo htmlspecialchars($searchQuery); ?>"
                        <?php if($searchField !== 'all'): ?>
                            in <strong><?php echo ucfirst(str_replace('_', ' ', $searchField)); ?></strong>
                        <?php endif; ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Order Details View -->
    <?php if($viewOrder): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #<?php echo $viewOrder['id']; ?> Details</h5>
                <div>
                    <a href="<?php echo BASE_URL; ?>admin/orders.php" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Orders
                    </a>
                    <a href="<?php echo BASE_URL; ?>orders/invoice.php?id=<?php echo $viewOrder['id']; ?>" 
                       class="btn btn-sm btn-info">
                        <i class="bi bi-receipt"></i> View Invoice
                    </a>
                    <?php if($viewOrder['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                            <input type="hidden" name="order_id" value="<?php echo $viewOrder['id']; ?>">
                            <button type="submit" name="delete_order" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Delete Order
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p>
                            <strong>Name:</strong> <?php echo htmlspecialchars($viewOrder['user_name']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($viewOrder['user_email']); ?><br>
                            <strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($viewOrder['created_at'])); ?><br>
                            <strong>Order Age:</strong> 
                            <?php 
                                $orderDate = new DateTime($viewOrder['created_at']);
                                $now = new DateTime();
                                $diff = $now->diff($orderDate);
                                if ($diff->days > 0) {
                                    echo $diff->days . ' day(s) old';
                                    if ($diff->days >= 1) {
                                        echo ' <span class="badge bg-danger">Expired</span>';
                                    }
                                } else {
                                    echo $diff->h . ' hour(s) old';
                                }
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Shipping Information</h6>
                        <p>
                            <strong>Address:</strong> <?php echo htmlspecialchars($viewOrder['shipping_address']); ?><br>
                            <strong>City:</strong> <?php echo htmlspecialchars($viewOrder['shipping_city']); ?><br>
                            <strong>Contact:</strong> <?php echo htmlspecialchars($viewOrder['shipping_zip']); ?>
                        </p>
                    </div>
                </div>
                
                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orderItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Le <?php echo number_format($item['price'], 2); ?></td>
                                    <td>Le <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th>Le <?php echo number_format($viewOrder['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <form method="POST" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="order_id" value="<?php echo $viewOrder['id']; ?>">
                            <label for="status" class="fw-bold me-2">Update Status:</label>
                            <select name="status" id="status" class="form-select" style="width: 150px;">
                                <option value="pending" <?php echo $viewOrder['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="paid" <?php echo $viewOrder['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="failed" <?php echo $viewOrder['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary">
                                <i class="bi bi-check"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- All Orders List -->
    <?php if(!$viewOrder): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Orders</h5>
                <span class="text-muted small">
                    <i class="bi bi-clock-history"></i> Pending orders older than 24h are auto-deleted
                </span>
            </div>
            <div class="card-body">
                <?php if(empty($orders)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                        <p class="text-muted mt-2">No orders found</p>
                        <?php if(!empty($searchQuery)): ?>
                            <p class="text-muted small">Try adjusting your search criteria</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="ordersTable">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Age</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): 
                                    $orderDate = new DateTime($order['created_at']);
                                    $now = new DateTime();
                                    $diff = $now->diff($orderDate);
                                    $isOld = $diff->days >= 1;
                                ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo $order['id']; ?></strong>
                                            <?php if(!empty($searchQuery) && $searchField === 'order_id'): ?>
                                                <span class="badge bg-primary">Match</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($order['user_name']); ?>
                                            <?php if(!empty($searchQuery) && $searchField === 'customer'): ?>
                                                <span class="badge bg-primary">Match</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Le <?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <?php if($order['status'] === 'paid'): ?>
                                                <span class="badge bg-success">Paid</span>
                                            <?php elseif($order['status'] === 'pending'): ?>
                                                <span class="badge bg-warning <?php echo $isOld ? 'text-danger' : ''; ?>">
                                                    Pending <?php if($isOld): ?><i class="bi bi-exclamation-triangle"></i><?php endif; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <?php if($diff->days > 0): ?>
                                                <span class="badge bg-danger"><?php echo $diff->days; ?> day(s)</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo $diff->h; ?> hour(s)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo BASE_URL; ?>admin/orders.php?view=<?php echo $order['id']; ?>" 
                                                   class="btn btn-primary" title="View Order Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>orders/invoice.php?id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-info" title="View Invoice">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
                                                <?php if($order['status'] === 'pending'): ?>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <button type="submit" name="delete_order" class="btn btn-danger" title="Delete Order">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.btn-group .btn {
    border-radius: 4px;
    margin: 0 2px;
}

.btn-group form {
    display: inline-block;
}

.badge .bi-exclamation-triangle {
    margin-left: 4px;
}

.table td {
    vertical-align: middle;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-group .btn-danger {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group-text {
    background: #f8f9fa;
}

/* Search highlight */
.badge.bg-primary {
    font-size: 0.6rem;
    padding: 3px 6px;
    margin-left: 5px;
}

/* Responsive search */
@media (max-width: 768px) {
    .search-form .col-md-5,
    .search-form .col-md-3,
    .search-form .col-md-2 {
        margin-bottom: 10px;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>