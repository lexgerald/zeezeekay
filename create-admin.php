<?php
// create-admin.php - Run this once to create admin user
require_once 'config/config.php';
require_once 'config/db.php';

$db = getDB();

$name = 'Admin User';
$email = 'admin@mail.com';
$password = password_hash('admin123', PASSWORD_BCRYPT);
$role = 'admin';

try {
    // Check if admin already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo "Admin user already exists!<br>";
        echo "Email: admin@mail.com<br>";
        echo "Password: admin123<br>";
    } else {
        // Insert admin user
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        echo "Admin user created successfully!<br>";
        echo "Email: admin@mail.com<br>";
        echo "Password: admin123<br>";
        echo "<a href='auth/login.php'>Login here</a>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>