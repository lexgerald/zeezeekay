<?php
// api/products.php - API endpoint for frontend
require_once '../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = getDB();
    $stmt = $db->query("SELECT id, name, description, price, image, stock, category, featured FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
    
    // Add image URLs
    foreach ($products as &$product) {
        $product['image'] = $product['image'] ?: 'placeholder.png';
        $product['image_url'] = '../assets/images/' . $product['image'];
        $product['price_formatted'] = '$' . number_format($product['price'], 2);
    }
    
    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}