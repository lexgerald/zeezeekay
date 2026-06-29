<?php
// checkout/order-success.php - Order confirmation page with loading animation
require_once '../config/config.php';
require_once '../config/db.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId > 0 && isset($_SESSION['user_id'])) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();
} else {
    $order = null;
}

// Get user name
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Zeekay Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/zee_logo.png" type="image/x-icon">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        /* Loading Screen Styles */
        .loading-container {
            background: white;
            border-radius: 24px;
            padding: 50px 60px 40px;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .loading-icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .loading-icon {
            font-size: 3.5rem;
            color: white;
        }
        
        .loading-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }
        
        .loading-subtitle {
            color: #718096;
            font-size: 1rem;
        }
        
        .spinner-container {
            margin: 30px 0 20px;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.25rem;
            color: #28a745;
        }
        
        .progress-container {
            margin: 20px 0;
        }
        
        .progress {
            height: 6px;
            border-radius: 10px;
            background: #e2e8f0;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #28a745, #20c997);
            width: 0%;
            transition: width 0.3s ease;
            position: relative;
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.3), 
                transparent
            );
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        .loading-text {
            color: #a0aec0;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        
        .loading-text i {
            color: #28a745;
        }
        
        .dots {
            display: inline-block;
        }
        
        .dots span {
            animation: dots 1.4s infinite;
            opacity: 0;
        }
        
        .dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes dots {
            0%, 20% { opacity: 0; }
            40% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        /* Success Message Styles (hidden initially) */
        .success-container {
            display: none;
            background: white;
            border-radius: 24px;
            padding: 50px 60px 40px;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            animation: bounceIn 0.8s ease;
        }
        
        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            margin: 20px 0 10px;
        }
        
        .success-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 10px 30px;
            border-radius: 50px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 480px) {
            .loading-container, .success-container {
                padding: 30px 20px;
            }
            
            .loading-title {
                font-size: 1.4rem;
            }
            
            .success-title {
                font-size: 1.6rem;
            }
            
            .loading-icon-wrapper {
                width: 80px;
                height: 80px;
            }
            
            .loading-icon {
                font-size: 2.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-container" id="loadingScreen">
        <div class="loading-icon-wrapper">
            <i class="bi bi-check-circle-fill loading-icon"></i>
        </div>
        
        <h1 class="loading-title">Processing Your Order</h1>
        <p class="loading-subtitle">
            Please wait while we confirm your order, <?php echo htmlspecialchars($userName); ?>
        </p>
        
        <div class="spinner-container">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <div class="progress-container">
            <div class="progress">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>
        
        <div class="loading-text">
            <i class="bi bi-arrow-repeat"></i> 
            Confirming your order<span class="dots"><span>.</span><span>.</span><span>.</span></span>
        </div>
    </div>
    
    <!-- Success Message -->
    <div class="success-container" id="successScreen">
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        
        <h1 class="success-title">Order Placed Successfully! 🎉</h1>
        <p class="success-subtitle">Thank you for your order, <?php echo htmlspecialchars($userName); ?>!</p>
        
        <?php if($order): ?>
            <div class="alert alert-info text-start">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Order #:</strong> <?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?><br>
                        <strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Total:</strong> Le <?php echo number_format($order['total_amount'], 2); ?><br>
                        <strong>Status:</strong> <span class="badge bg-warning">Pending</span>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-clock-history"></i> 
                We will contact you within 24 hours to confirm your order.
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

    <script>
        // Show loading screen with progress animation
        let progress = 0;
        const progressBar = document.getElementById('progressBar');
        const loadingScreen = document.getElementById('loadingScreen');
        const successScreen = document.getElementById('successScreen');
        
        // Function to update progress
        function updateProgress() {
            progress += 2;
            if (progress > 100) {
                progress = 100;
            }
            progressBar.style.width = progress + '%';
        }
        
        // Function to show success screen
        function showSuccess() {
            loadingScreen.style.display = 'none';
            successScreen.style.display = 'block';
        }
        
        // Start progress animation (2.5 seconds loading)
        let interval = setInterval(function() {
            updateProgress();
            
            if (progress >= 100) {
                clearInterval(interval);
                // Small delay before showing success
                setTimeout(showSuccess, 300);
            }
        }, 25); // 25ms * 100 = 2.5 seconds
        
        // Also allow click to skip loading (optional)
        loadingScreen.addEventListener('click', function() {
            if (progress < 100) {
                progress = 100;
                progressBar.style.width = '100%';
                clearInterval(interval);
                setTimeout(showSuccess, 300);
            }
        });
        
        // Keyboard shortcut: Press any key to skip loading
        document.addEventListener('keydown', function(e) {
            if (progress < 100) {
                progress = 100;
                progressBar.style.width = '100%';
                clearInterval(interval);
                setTimeout(showSuccess, 300);
            }
        });
    </script>
</body>
</html>
<?php
?>