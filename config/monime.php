<?php
// config/monime.php - Monime Payment Integration

class MonimePayment {
    private $accessToken;
    private $spaceId;
    private $apiVersion;
    private $baseUrl;
    private $webhookUrl;
    private $successUrl;
    private $cancelUrl;
    private $isTestMode;
    
    public function __construct() {
        // Load environment variables from .env file
        $this->loadEnv();
        
        // Get credentials from environment - trim any quotes
        $this->accessToken = trim(getenv('MONIME_ACCESS_TOKEN') ?: '', "'");
        $this->spaceId = trim(getenv('MONIME_SPACE_ID') ?: '', "'");
        $this->apiVersion = trim(getenv('MONIME_API_VERSION') ?: 'caph.2025-08-23', "'");
        $this->baseUrl = trim(getenv('MONIME_BASE_URL') ?: 'https://api.monime.io', "'");
        $this->isTestMode = strpos($this->accessToken, 'mon_test') !== false;
        
        $appUrl = trim(getenv('APP_URL') ?: 'http://localhost/zeekay-store', "'");
        $this->webhookUrl = $appUrl . '/api/monime-webhook.php';
        $this->successUrl = $appUrl . '/checkout/success.php';
        $this->cancelUrl = $appUrl . '/checkout/failed.php';
        
        // Log initialization
        $this->logMessage("=== MONIME INITIALIZED ===");
        $this->logMessage("Space ID: " . $this->spaceId);
        $this->logMessage("Token: " . substr($this->accessToken, 0, 20) . "...");
        $this->logMessage("API Version: " . $this->apiVersion);
        $this->logMessage("Base URL: " . $this->baseUrl);
        $this->logMessage("Test Mode: " . ($this->isTestMode ? 'Yes' : 'No'));
    }
    
    private function loadEnv() {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                $line = trim($line);
                if (strpos($line, '#') === 0 || empty($line)) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value, " '\"");
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        } else {
            $this->logMessage("WARNING: .env file not found at: " . __DIR__ . '/../.env');
        }
    }
    
    /**
     * Create a checkout session for Monime
     */
    public function createCheckoutSession($orderId, $amount, $currency = 'SLE', $description = 'Zeekay Store Order') {
        // Validate credentials
        if (empty($this->accessToken)) {
            $this->logMessage("ERROR: Missing Monime access token");
            return ['success' => false, 'message' => 'Payment gateway not configured - missing token'];
        }
        
        if (empty($this->spaceId)) {
            $this->logMessage("ERROR: Missing Monime space ID");
            return ['success' => false, 'message' => 'Payment gateway not configured - missing space ID'];
        }
        
        // Validate amount
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Invalid amount'];
        }
        
        // Generate unique idempotency key
        $idempotencyKey = uniqid('zk_', true);
        
        // Prepare payload for Checkout Session API
        $payload = [
            'name' => $description,
            'description' => "Order #{$orderId} - Zeekay Store",
            'lineItems' => [
                [
                    'name' => "Order #{$orderId} - Zeekay Store",
                    'amount' => (int)($amount * 100),
                    'quantity' => 1,
                    'currency' => $currency
                ]
            ],
            'successUrl' => $this->successUrl . '?order_id=' . $orderId,
            'cancelUrl' => $this->cancelUrl . '?order_id=' . $orderId,
            'reference' => (string)$orderId,
            'metadata' => [
                'order_id' => $orderId,
                'user_id' => $_SESSION['user_id'] ?? null
            ]
        ];
        
        $this->logMessage("=== NEW PAYMENT REQUEST ===");
        $this->logMessage("Order ID: $orderId");
        $this->logMessage("Amount: $amount $currency");
        $this->logMessage("Space ID: " . $this->spaceId);
        $this->logMessage("Full URL: " . $this->baseUrl . '/v1/checkout-sessions');
        $this->logMessage("Payload: " . json_encode($payload));
        
        // Initialize cURL
        $ch = curl_init($this->baseUrl . '/v1/checkout-sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Monime-Space-Id: ' . $this->spaceId,
            'Monime-Version: ' . $this->apiVersion,
            'Idempotency-Key: ' . $idempotencyKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $this->logMessage("HTTP Code: $httpCode");
        $this->logMessage("Response: " . $response);
        
        if ($error) {
            $this->logMessage("cURL Error: " . $error);
            return ['success' => false, 'message' => 'Connection error: ' . $error];
        }
        
        // Parse response
        $result = json_decode($response, true);
        
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->logMessage("JSON Parse Error: " . json_last_error_msg());
            return ['success' => false, 'message' => 'Invalid response from payment gateway'];
        }
        
        // Handle response
        if ($httpCode === 200 || $httpCode === 201) {
            // Check if there's an error
            if (isset($result['error'])) {
                $errorMsg = is_array($result['error']) ? 
                    ($result['error']['message'] ?? json_encode($result['error'])) : 
                    $result['error'];
                $this->logMessage("API Error: " . $errorMsg);
                return ['success' => false, 'message' => $errorMsg];
            }
            
            // Check for success
            if (isset($result['success']) && $result['success'] === true) {
                $redirectUrl = null;
                
                // Find redirect URL
                if (isset($result['result']['redirectUrl'])) {
                    $redirectUrl = $result['result']['redirectUrl'];
                } elseif (isset($result['result']['checkoutUrl'])) {
                    $redirectUrl = $result['result']['checkoutUrl'];
                } elseif (isset($result['result']['paymentUrl'])) {
                    $redirectUrl = $result['result']['paymentUrl'];
                } elseif (isset($result['result']['url'])) {
                    $redirectUrl = $result['result']['url'];
                } elseif (isset($result['redirectUrl'])) {
                    $redirectUrl = $result['redirectUrl'];
                } elseif (isset($result['checkoutUrl'])) {
                    $redirectUrl = $result['checkoutUrl'];
                } elseif (isset($result['paymentUrl'])) {
                    $redirectUrl = $result['paymentUrl'];
                } elseif (isset($result['url'])) {
                    $redirectUrl = $result['url'];
                }
                
                if ($redirectUrl) {
                    $this->logMessage("SUCCESS: Redirect URL found: " . $redirectUrl);
                    return [
                        'success' => true,
                        'payment_url' => $redirectUrl,
                        'transaction_id' => $result['result']['orderNumber'] ?? $result['orderNumber'] ?? '',
                        'order_id' => $orderId,
                        'session_id' => $result['result']['id'] ?? $result['id'] ?? ''
                    ];
                } else {
                    $this->logMessage("ERROR: No redirect URL in response");
                    return ['success' => false, 'message' => 'Payment URL not found in response'];
                }
            } else {
                // Check for error messages
                $errorMsg = 'Payment gateway error';
                if (isset($result['messages']) && is_array($result['messages'])) {
                    $errorMsg = implode(', ', $result['messages']);
                } elseif (isset($result['message'])) {
                    $errorMsg = $result['message'];
                }
                $this->logMessage("ERROR: $errorMsg");
                return ['success' => false, 'message' => $errorMsg];
            }
        } else {
            // Handle HTTP error
            $errorMsg = 'Payment gateway error (HTTP ' . $httpCode . ')';
            if (isset($result['error']['message'])) {
                $errorMsg = $result['error']['message'];
            } elseif (isset($result['message'])) {
                $errorMsg = $result['message'];
            }
            $this->logMessage("ERROR: $errorMsg");
            return ['success' => false, 'message' => $errorMsg, 'code' => $httpCode];
        }
    }
    
    private function logMessage($message) {
        $logFile = __DIR__ . '/../logs/monime.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents($logFile, 
            date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, 
            FILE_APPEND
        );
    }
}

// Helper function
function createMonimePayment($orderId, $amount) {
    $monime = new MonimePayment();
    return $monime->createCheckoutSession($orderId, $amount);
}
?>