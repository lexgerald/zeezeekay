<?php
// api/monime-webhook.php
require_once '../config/config.php';
require_once '../config/db.php';

// Log webhook calls
function logWebhook($data) {
    $logFile = __DIR__ . '/../logs/webhook.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . json_encode($data) . "\n", FILE_APPEND);
}

// Get raw POST data
$rawPayload = file_get_contents('php://input');
$payload = json_decode($rawPayload, true);

logWebhook(['payload' => $payload, 'headers' => getallheaders()]);

if (!$payload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

try {
    $db = getDB();
    
    // Extract order ID from payload
    $orderId = $payload['order_id'] ?? $payload['reference'] ?? null;
    $status = $payload['status'] ?? $payload['payment_status'] ?? '';
    
    if ($orderId) {
        // Map Monime status to our status
        $orderStatus = 'pending';
        if (in_array($status, ['completed', 'paid', 'success', 'succeeded'])) {
            $orderStatus = 'paid';
        } elseif (in_array($status, ['failed', 'cancelled', 'expired'])) {
            $orderStatus = 'failed';
        }
        
        // Update order status
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$orderStatus, $orderId]);
        
        // Update payment record
        $stmt = $db->prepare("UPDATE payments SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $orderId]);
        
        logWebhook(['success' => true, 'order_id' => $orderId, 'status' => $orderStatus]);
        
        http_response_code(200);
        echo json_encode(['success' => true]);
    } else {
        logWebhook(['error' => 'Missing order ID']);
        http_response_code(400);
        echo json_encode(['error' => 'Missing required data']);
    }
    
} catch (Exception $e) {
    logWebhook(['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}