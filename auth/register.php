<?php
// auth/register.php
require_once '../config/config.php';
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            $db = getDB();
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                // Insert user
                $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashedPassword]);
                
                $success = 'Registration successful! You can now login.';
            }
        } catch (PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zeekay Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
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
            background: linear-gradient(135deg, #667eea 0%, #17b8d8 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #17b8d8 100%);
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
            color: #62bdeb;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .auth-links a:hover {
            color: #17b8d8;
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
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 5px;
            transition: width 0.3s ease;
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
                            <!--<i class="bi bi-person-plus brand-icon"></i>-->
                            <img src="../assets/images/zee_logo.png" alt="" width="60" height="50">
                            <div>
                                <div class="brand-text">Zeekay</div>
                                <div class="brand-sub">Create Account</div>
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
                        
                        <?php if($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-person"></i> Full Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Enter your full name" required>
                            </div>
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
                                       placeholder="Min 6 characters" required>
                                <div class="password-strength" id="passwordStrength"></div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label fw-semibold">
                                    <i class="bi bi-shield-lock"></i> Confirm Password
                                </label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-person-plus"></i> Register
                            </button>
                        </form>
                        
                        <div class="auth-links">
                            <a href="<?php echo BASE_URL; ?>auth/login.php" class="login-link">
                                <i class="bi bi-box-arrow-in-right"></i> Login here
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
    <script>
        // Simple password strength indicator
        document.getElementById('password')?.addEventListener('input', function() {
            const strength = document.getElementById('passwordStrength');
            const password = this.value;
            let width = '0%';
            let color = '#dc3545';
            
            if (password.length > 0) {
                if (password.length < 4) {
                    width = '25%';
                    color = '#dc3545';
                } else if (password.length < 6) {
                    width = '50%';
                    color = '#ffc107';
                } else if (password.length < 8) {
                    width = '75%';
                    color = '#17a2b8';
                } else {
                    width = '100%';
                    color = '#28a745';
                }
            }
            
            strength.style.width = width;
            strength.style.backgroundColor = color;
        });
    </script>
</body>
</html>