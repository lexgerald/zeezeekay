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
        /* Print Styles - Compact Version */
        @media print {
            /* Page setup */
            @page {
                size: A4;
                margin: 8mm 10mm;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 10pt !important;
                line-height: 1.4 !important;
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
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .card-body {
                padding: 10px 0 !important;
            }
            
            .card-header {
                background-color: #0d6efd !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 8px 15px !important;
                border-radius: 4px 4px 0 0 !important;
            }
            
            .card-header h4 {
                font-size: 14pt !important;
                margin: 0 !important;
            }
            
            .badge {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 8pt !important;
                padding: 2px 8px !important;
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
            
            /* Compact header */
            .invoice-header {
                border-bottom: 2px solid #0d6efd !important;
                padding-bottom: 8px !important;
                margin-bottom: 10px !important;
            }
            
            .invoice-header .company-name {
                font-size: 16pt !important;
                font-weight: 700 !important;
                color: #0d6efd !important;
            }
            
            .invoice-header .invoice-title {
                font-size: 12pt !important;
                font-weight: 600 !important;
                color: #6c757d !important;
            }
            
            .invoice-header .text-muted {
                font-size: 8pt !important;
            }
            
            /* Compact details */
            .invoice-details {
                background: #f8f9fa !important;
                padding: 8px 12px !important;
                border-radius: 4px !important;
                margin-bottom: 10px !important;
            }
            
            .invoice-details h6 {
                font-size: 9pt !important;
                font-weight: 700 !important;
                margin-bottom: 2px !important;
            }
            
            .invoice-details p {
                font-size: 8.5pt !important;
                margin: 0 !important;
                line-height: 1.3 !important;
            }
            
            /* Compact table */
            .table {
                font-size: 8.5pt !important;
                margin-bottom: 5px !important;
            }
            
            .table th {
                font-size: 8.5pt !important;
                padding: 3px 6px !important;
                background-color: #e9ecef !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .table td {
                padding: 3px 6px !important;
                font-size: 8.5pt !important;
            }
            
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .table tfoot th {
                font-size: 8.5pt !important;
                padding: 3px 6px !important;
            }
            
            .table tfoot .table-primary {
                background-color: #0d6efd !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .table tfoot .table-primary th {
                color: white !important;
            }
            
            /* Compact collection notice */
            .collection-notice {
                background-color: #fff3cd !important;
                border-left: 3px solid #ffc107 !important;
                padding: 6px 12px !important;
                border-radius: 4px !important;
                margin: 8px 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .collection-notice i {
                font-size: 12pt !important;
                color: #ffc107 !important;
            }
            
            .collection-notice .notice-title {
                font-weight: 700 !important;
                color: #856404 !important;
                font-size: 9pt !important;
            }
            
            .collection-notice .notice-text {
                color: #856404 !important;
                font-size: 8pt !important;
                margin: 0 !important;
                line-height: 1.3 !important;
            }
            
            .collection-notice small {
                font-size: 7.5pt !important;
            }
            
            /* Hide navbar, footer, and action buttons */
            .navbar, footer, .no-print, .btn {
                display: none !important;
            }
            
            .watermark {
                display: none !important;
            }
            
            /* Invoice footer */
            .invoice-footer-text {
                margin-top: 10px !important;
                padding-top: 8px !important;
                border-top: 1px solid #dee2e6 !important;
                text-align: center !important;
                font-size: 7.5pt !important;
                color: #6c757d !important;
            }
            
            .invoice-footer-text p {
                margin: 0 !important;
                font-size: 8pt !important;
            }
            
            .invoice-footer-text small {
                font-size: 7pt !important;
            }
            
            .status-badge {
                font-size: 8pt !important;
                padding: 2px 10px !important;
            }
            
            /* Ensure everything fits on one page */
            .card-body {
                padding: 5px 0 !important;
            }
            
            /* Force page break control */
            .page-break {
                page-break-after: avoid;
            }
            
            /* Reduce margins on print */
            .container {
                padding: 0 !important;
            }
            
            .row {
                margin: 0 !important;
            }
            
            .col-md-6 {
                padding: 0 5px !important;
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
        
        .invoice-details h6 {
            font-size: 1rem;
            font-weight: 700;
        }
        
        .invoice-details p {
            margin-bottom: 0;
        }
        
        .invoice-details table {
            margin-bottom: 0;
        }
        
        .invoice-details table td {
            padding: 5px 10px;
            border: none;
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
                            <div class="company-name">Zeekay</div>
                            <div class="text-muted">
<<<<<<< HEAD
                                <i class="bi bi-geo-alt"></i> Hamilton Junction No2 River Quarry Junction, Peninsular Hwy, Freetown<br>
                                <i class="bi bi-envelope"></i> info@zeezeekay.com
=======
                                <i class="bi bi-geo-alt"></i> #13 Commerce Street, Freetown<br>
                                <i class="bi bi-envelope"></i> support@zeekay.com
>>>>>>> e93998f4215d06f7f5dd36a803fd172ee105ec1a
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="invoice-title">INVOICE</div>
                            <div class="text-muted">
                                <strong>Invoice #:</strong> <?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?><br>
                                <strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Details -->
                <div class="invoice-details">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-person"></i> Bill To:</h6>
                            <p>
                                <strong><?php echo htmlspecialchars($order['user_name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_zip']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-info-circle"></i> Order Details:</h6>
                            <p>
                                <strong>Order ID:</strong> #<?php echo $order['id']; ?><br>
                                <strong>Status:</strong> 
                                <span class="badge <?php 
                                    echo $order['status'] === 'paid' ? 'bg-success' : 
                                        ($order['status'] === 'pending' ? 'bg-warning' : 'bg-danger'); 
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
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
                                <th style="width: 5%;">#</th>
                                <th style="width: 45%;">Product</th>
                                <th style="width: 15%;" class="text-center">Qty</th>
                                <th style="width: 17%;" class="text-end">Price</th>
                                <th style="width: 18%;" class="text-end">Subtotal</th>
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
                                <th colspan="4" class="text-end" style="font-size: 1.1rem;">Total:</th>
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
                            Please collect your goods within <strong>7 days</strong> from order date.
                            <br>
                            <strong>Deadline:</strong> <span class="deadline-date"><?php echo $deadlineFormatted; ?></span>
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
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </div>
                </div>
                
                <!-- Invoice Footer -->
                <div class="invoice-footer-text">
                    <p class="mb-0">
                        <i class="bi bi-check-circle text-success"></i> 
<<<<<<< HEAD
                        Thank you for shopping with Zeekay!
=======
                        Thank you for shopping with Zeekay Store!
>>>>>>> e93998f4215d06f7f5dd36a803fd172ee105ec1a
                    </p>
                    <small>This is a system-generated invoice.</small>
                </div>
            </div>
        </div>
    </div>

    <?php if(!isset($_GET['print'])): ?>
        <?php require_once '../includes/footer.php'; ?>
    <?php else: ?>
        <script>
            window.print();
        </script>
    <?php endif; ?>
    
    <script>
        <?php if(isset($_GET['print'])): ?>
            window.onload = function() {
                window.print();
            };
        <?php endif; ?>
        
        document.querySelector('[onclick="window.print()"]')?.addEventListener('click', function(e) {
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Preparing...';
            setTimeout(() => {
                this.innerHTML = '<i class="bi bi-printer"></i> Print Invoice';
            }, 2000);
        });
    </script>
</body>
</html>