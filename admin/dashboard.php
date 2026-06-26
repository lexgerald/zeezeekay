<?php
// admin/dashboard.php
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

// Get statistics
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Get revenue
$revenueStmt = $db->query("SELECT SUM(total_amount) FROM orders WHERE status = 'paid'");
$totalRevenue = $revenueStmt->fetchColumn() ?: 0;

// Get pending orders count
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$paidOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();

// Recent orders
$recentOrders = $db->query("SELECT o.*, u.name as user_name 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.id 
                             ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

// Welcome message
$greeting = '';
$hour = date('H');
if ($hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}
?>
<div class="container mt-4">
    <!-- Admin Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-speedometer2"></i> Admin Dashboard</h2>
            <p class="text-muted"><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">
                <i class="bi bi-house"></i> View Store
            </a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Revenue</h6>
                            <h2 class="mb-0">Le <?php echo number_format($totalRevenue, 2); ?></h2>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Orders</h6>
                            <h2 class="mb-0"><?php echo $totalOrders; ?></h2>
                            <small><?php echo $pendingOrders; ?> pending</small>
                        </div>
                        <i class="bi bi-box" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Products</h6>
                            <h2 class="mb-0"><?php echo $totalProducts; ?></h2>
                        </div>
                        <i class="bi bi-box-seam" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Users</h6>
                            <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo BASE_URL; ?>admin/products.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Product
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/orders.php" class="btn btn-success">
                            <i class="bi bi-eye"></i> View All Orders
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/users.php" class="btn btn-info text-white">
                            <i class="bi bi-people"></i> Manage Users
                        </a>
                        <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-warning">
                            <i class="bi bi-box-seam"></i> View Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="<?php echo BASE_URL; ?>admin/orders.php" class="btn btn-sm btn-primary">View All Orders</a>
                </div>
                <div class="card-body">
                    <?php if(!empty($recentOrders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                            <td>Le <?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <?php if($order['status'] === 'paid'): ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php elseif($order['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>admin/orders.php?view=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>orders/invoice.php?id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="bi bi-receipt"></i> Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">No orders yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>