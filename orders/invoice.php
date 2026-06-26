<?php
// orders/invoice.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$db = getDB();
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get order details
$query = "SELECT o.*, u.name as user_name, u.email as user_email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = ?";
if (!$isAdmin) {
    $query .= " AND o.user_id = ?";
}
$stmt = $db->prepare($query);
if (!$isAdmin) {
    $stmt->execute([$orderId, $userId]);
} else {
    $stmt->execute([$orderId]);
}
$order = $stmt->fetch();

if (!$order) {
    header("Location: " . BASE_URL . "orders/orders.php");
    exit;
}

// Get order items
$stmt = $db->prepare("SELECT oi.*, p.name as product_name FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

// Determine back link
$backLink = BASE_URL . "orders/orders.php";
if ($isAdmin && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin/orders.php') !== false) {
    $backLink = BASE_URL . "admin/orders.php";
}

// Calculate collection deadline (7 days from order date)
$orderDate = new DateTime($order['created_at']);
$deadline = clone $orderDate;
$deadline->modify('+7 days');
$deadlineFormatted = $deadline->format('F d, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order['id']; ?> - Zeekay Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <style>
        /* Print Styles */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            
            .card-header {
                background-color: #0d6efd !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .badge {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .bg-success {
                background-color: #198754 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .bg-warning {
                background-color: #ffc107 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .bg-danger {
                background-color: #dc3545 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .invoice-footer {
                margin-top: 50px;
                border-top: 2px solid #dee2e6;
                padding-top: 20px;
                text-align: center;
                font-size: 0.9rem;
                color: #6c757d;
            }
            
            /* Hide navbar and footer when printing */
            .navbar, footer, .no-print {
                display: none !important;
            }
            
            /* Ensure card body padding is preserved */
            .card-body {
                padding: 2rem !important;
            }
            
            .collection-notice {
                background-color: #fff3cd !important;
                border-color: #ffc107 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        /* Screen Styles */
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .invoice-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .invoice-header .company-name {
            font-size: 2rem;
            font-weight: 700;
            color: #0d6efd;
        }
        
        .invoice-header .invoice-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #6c757d;
        }
        
        .invoice-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .invoice-details table {
            margin-bottom: 0;
        }
        
        .invoice-details table td {
            padding: 5px 10px;
            border: none;
        }
        
        .invoice-total {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .invoice-total .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d6efd;
        }
        
        .invoice-footer-text {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            color: #6c757d;
        }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 5px 15px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 5rem;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }
        
        .collection-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .collection-notice i {
            font-size: 1.5rem;
            color: #ffc107;
            margin-top: 2px;
        }
        
        .collection-notice .notice-content {
            flex: 1;
        }
        
        .collection-notice .notice-title {
            font-weight: 700;
            color: #856404;
            margin-bottom: 4px;
        }
        
        .collection-notice .notice-text {
            color: #856404;
            margin-bottom: 0;
        }
        
        .collection-notice .deadline-date {
            font-weight: 700;
            color: #dc3545;
        }
        
        @media print {
            .watermark {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">ZEKAY STORE</div>
    
    <?php if(!isset($_GET['print'])): ?>
        <!-- Only show navbar when not printing -->
        <?php require_once '../includes/navbar.php'; ?>
    <?php endif; ?>
    
    <div class="container mt-4 invoice-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <i class="bi bi-receipt"></i> Invoice #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                        </h4>
                    </div>
                    <div>
                        <span class="badge <?php 
                            echo $order['status'] === 'paid' ? 'bg-success' : 
                                ($order['status'] === 'pending' ? 'bg-warning' : 'bg-danger'); 
                        ?> status-badge">
                            <?php echo strtoupper($order['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Invoice Header -->
                <div class="invoice-header">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="company-name">Zeekay Store</div>
                            <div class="text-muted">
                                <i class="bi bi-geo-alt"></i> #13 Commerce Street, Freetown<br>
                                <i class="bi bi-envelope"></i> support@zeekay.com<br>
                                <i class="bi bi-phone"></i> +1 (555) 123-4567
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="invoice-title">INVOICE</div>
                            <div class="text-muted">
                                <strong>Invoice #:</strong> <?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?><br>
                                <strong>Date:</strong> <?php echo date('F d, Y', strtotime($order['created_at'])); ?><br>
                                <strong>Time:</strong> <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Details -->
                <div class="invoice-details">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-person"></i> Bill To:</h6>
                            <p class="mb-0">
                                <strong><?php echo htmlspecialchars($order['user_name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['user_email']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_zip']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-info-circle"></i> Order Details:</h6>
                            <p class="mb-0">
                                <strong>Order ID:</strong> #<?php echo $order['id']; ?><br>
                                <strong>Status:</strong> 
                                <span class="badge <?php 
                                    echo $order['status'] === 'paid' ? 'bg-success' : 
                                        ($order['status'] === 'pending' ? 'bg-warning' : 'bg-danger'); 
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span><br>
                                <strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <h6><i class="bi bi-box"></i> Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            <?php foreach($items as $item): ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end">Le <?php echo number_format($item['price'], 2); ?></td>
                                    <td class="text-end">Le <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <th class="text-end">Le <?php echo number_format($order['total_amount'], 2); ?></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Tax (0%):</th>
                                <th class="text-end">Le 0.00</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Shipping:</th>
                                <th class="text-end">Le 0.00</th>
                            </tr>
                            <tr class="table-primary">
                                <th colspan="4" class="text-end" style="font-size: 1.1rem;">Total Amount:</th>
                                <th class="text-end" style="font-size: 1.1rem;">
                                    <strong>Le <?php echo number_format($order['total_amount'], 2); ?></strong>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Collection Notice - 7 Days -->
                <div class="collection-notice">
                    <i class="bi bi-clock-history"></i>
                    <div class="notice-content">
                        <div class="notice-title">⚠️ Collection Notice</div>
                        <p class="notice-text">
                            Please collect your goods within <strong>seven (7) days</strong> from the order date.
                            <br>
                            <strong>Collection Deadline:</strong> <span class="deadline-date"><?php echo $deadlineFormatted; ?></span>
                            <br>
                            <small>If you fail to collect within this period, your order may be subject to cancellation.</small>
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-4 no-print">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo $backLink; ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Orders
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer"></i> Print Invoice
                        </button>
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="bi bi-download"></i> Download PDF
                        </button>
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Only show footer when not printing -->
    <?php if(!isset($_GET['print'])): ?>
        <?php require_once '../includes/footer.php'; ?>
    <?php else: ?>
        <script>
            window.print();
        </script>
    <?php endif; ?>
    
    <script>
        // Auto-print if print parameter is set
        <?php if(isset($_GET['print'])): ?>
            window.onload = function() {
                window.print();
            };
        <?php endif; ?>
        
        // Handle print button click with better UX
        document.querySelector('[onclick="window.print()"]')?.addEventListener('click', function(e) {
            // Optional: Show loading state
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Preparing...';
            setTimeout(() => {
                this.innerHTML = '<i class="bi bi-printer"></i> Print Invoice';
            }, 2000);
        });
    </script>
</body>
</html>