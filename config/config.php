<?php
// config/config.php - Base URL configuration

// Detect base URL automatically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($scriptName, '/') . '/';

// Define base URL constant
//define('BASE_URL', $protocol . $host . $basePath);
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . $basePath);

// For XAMPP, if auto-detection doesn't work, uncomment and set manually:
define('BASE_URL', 'http://localhost/zeekay-store/');
// define('BASE_PATH', 'C:/xampp/htdocs/zeekay-store/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug - uncomment to see your BASE_URL
// echo "BASE_URL: " . BASE_URL;
?>