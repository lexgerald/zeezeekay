<?php
// auth/login.php
require_once '../config/config.php';
require_once '../config/db.php';

$error = '';
$loginSuccess = false;
$redirectUrl = BASE_URL . 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                $loginSuccess = true;
                $redirectUrl = $_GET['redirect'] ?? BASE_URL . 'index.php';
            } else {
                $error = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}

// If login was successful, show loading screen
if ($loginSuccess) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting - Zeekay Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/zee_logo.png" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        
        .loading-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 50px 60px 40px;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 90%;
            animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .loading-icon-wrapper::before {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            opacity: 0.3;
            animation: ripple 2s ease-out infinite;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
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
            z-index: 1;
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
            margin-bottom: 5px;
        }
        
        .user-name {
            color: #764ba2;
            font-weight: 600;
        }
        
        .spinner-container {
            margin: 30px 0 20px;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.25rem;
            color: #764ba2;
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
            background: linear-gradient(90deg, #667eea, #764ba2);
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
            color: #764ba2;
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
        
        .welcome-message {
            margin-top: 15px;
            padding: 12px 20px;
            background: #f7fafc;
            border-radius: 12px;
            color: #4a5568;
            font-size: 0.9rem;
        }
        
        .welcome-message i {
            color: #48bb78;
            margin-right: 8px;
        }
        
        .skip-link {
            display: inline-block;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .skip-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .loading-container {
                padding: 35px 25px 30px;
            }
            
            .loading-icon-wrapper {
                width: 80px;
                height: 80px;
            }
            
            .loading-icon {
                font-size: 2.8rem;
            }
            
            .loading-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="loading-icon-wrapper">
            <i class="bi bi-check-circle-fill loading-icon"></i>
        </div>
        
        <h1 class="loading-title">Welcome Back!</h1>
        <p class="loading-subtitle">
            Hello, <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        </p>
        
        <div class="welcome-message">
            <i class="bi bi-check-circle-fill"></i> 
            Login successful! Redirecting you to the dashboard
        </div>
        
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
            Redirecting<span class="dots"><span>.</span><span>.</span><span>.</span></span>
        </div>
        
        <a href="<?php echo $redirectUrl; ?>" class="skip-link" id="skipLink">
            <i class="bi bi-arrow-right-circle"></i> Skip and continue now
        </a>
    </div>

    <script>
        // Redirect URL
        const redirectUrl = '<?php echo $redirectUrl; ?>';
        let seconds = 2; // Change this to adjust loading time
        const progressBar = document.getElementById('progressBar');
        
        // Function to redirect
        function performRedirect() {
            window.location.replace(redirectUrl);
        }
        
        // Update progress bar
        function updateProgress() {
            const progress = ((2 - seconds) / 2) * 100;
            progressBar.style.width = progress + '%';
        }
        
        // Start countdown
        const interval = setInterval(function() {
            seconds--;
            updateProgress();
            
            if (seconds <= 0) {
                clearInterval(interval);
                // Small delay before redirect for smooth experience
                setTimeout(performRedirect, 300);
            }
        }, 1000);
        
        // Initial progress update
        updateProgress();
        
        // Skip redirect
        document.getElementById('skipLink').addEventListener('click', function(e) {
            e.preventDefault();
            clearInterval(interval);
            performRedirect();
        });
        
        // Click anywhere to skip (optional)
        document.querySelector('.loading-container').addEventListener('click', function(e) {
            // Don't trigger if clicking on the skip link (it has its own handler)
            if (e.target.closest('#skipLink')) {
                return;
            }
            clearInterval(interval);
            performRedirect();
        });
        
        // Keyboard shortcut: Press any key to skip
        document.addEventListener('keydown', function(e) {
            // Ignore if typing in any input
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            clearInterval(interval);
            performRedirect();
        });
    </script>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zeekay Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/zee_logo.png" type="image/x-icon">
    <style>
        .auth-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .auth-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .auth-card .card-header {
            border-bottom: none;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .auth-card .card-body {
            padding: 2rem;
        }
        .auth-card .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .auth-card .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        .auth-card .btn-primary {
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .auth-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .auth-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .auth-links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .auth-links .home-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .brand-icon {
            font-size: 2.5rem;
            color: white;
            margin-right: 10px;
        }
        .brand-text {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .brand-sub {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <div class="card-header text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <img src="<?php echo BASE_URL; ?>assets/images/zee_logo.png" alt="Zeekay" width="60" height="50">
                            <div>
                                <div class="brand-text">Zeekay</div>
                                <div class="brand-sub">Welcome Back!</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>
                        
                        <div class="auth-links">
                            <a href="<?php echo BASE_URL; ?>auth/register.php" class="register-link">
                                <i class="bi bi-person-plus"></i> Register here
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php" class="home-link">
                                <i class="bi bi-house"></i> Home
                            </a>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Your data is secure with us
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>