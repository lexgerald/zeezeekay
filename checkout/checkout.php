<?php
// checkout/checkout.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php?redirect=" . urlencode('/checkout/checkout.php'));
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    header("Location: " . BASE_URL . "cart/cart.php");
    exit;
}

$db = getDB();
$cartItems = [];
$total = 0;

foreach($cart as $productId => $quantity) {
    $stmt = $db->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if ($product && $product['stock'] >= $quantity) {
        $product['quantity'] = $quantity;
        $product['subtotal'] = $product['price'] * $quantity;
        $cartItems[] = $product;
        $total += $product['subtotal'];
    }
}

if (empty($cartItems)) {
    header("Location: " . BASE_URL . "cart/cart.php");
    exit;
}

// Generate reference for Orange Money
$reference = 'ZK-' . time() . '-' . rand(1000, 9999);
?>
<div class="container mt-4">
    <h2>Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5>Delivery Information</h5>
                    <form method="POST" action="<?php echo BASE_URL; ?>checkout/place-order.php" id="checkoutForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter your Address" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="zip" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="zip" name="zip" placeholder="Enter phone number (088 123456)" required>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Order Items</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cartItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>Le <?php echo number_format($item['price'], 2); ?></td>
                                            <td>Le <?php echo number_format($item['subtotal'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>Le <?php echo number_format($total, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <input type="hidden" name="total" value="<?php echo $total; ?>">
                        <input type="hidden" name="reference" value="<?php echo $reference; ?>">
                        
                        <!-- Payment Options -->
                        <div class="mt-4">
                            <h5>Payment Method</h5>
                            <div class="row g-3">
                                <!-- Place Order Button -->
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success btn-lg w-100" id="placeOrderBtn">
                                        <i class="bi bi-check-circle"></i> Place Order
                                    </button>
                                    <small class="text-muted d-block text-center mt-1">
                                        <i class="bi bi-info-circle"></i> We will contact you for payment
                                    </small>
                                </div>
                                
                                <!-- Orange Money Button -->
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-warning btn-lg w-100" 
                                            onclick="processOrangeMoney(event)">
                                        <i class="bi bi-phone"></i> Pay with Orange Money
                                    </button>
                                    <small class="text-muted d-block text-center mt-1">
                                        <i class="bi bi-info-circle"></i> Dial *144*4*1*276193# to complete payment
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Order Summary</h5>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Items:</span>
                        <span><?php echo count($cartItems); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total:</span>
                        <span class="fw-bold text-success">Le <?php echo number_format($total, 2); ?></span>
                    </div>
                    <hr>
                    <?php if($total > 0): ?>
                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-phone"></i> 
                            <strong>Orange Money Payment:</strong><br>
                            Reference: <strong><?php echo $reference; ?></strong>
                        </small>
                    </div>
                    <?php endif; ?>
                    <div class="alert alert-info">
                        <small><i class="bi bi-shield-check"></i> Your order is secure.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation before submission for Place Order
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const zip = document.getElementById('zip').value.trim();
    
    if (!address || !city || !zip) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    // Show loading state on button
    const button = document.getElementById('placeOrderBtn');
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    button.disabled = true;
    
    return true;
});

// Process Orange Money payment
function processOrangeMoney(event) {
    const button = event.target.closest('button');
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const zip = document.getElementById('zip').value.trim();
    const total = <?php echo $total; ?>;
    const reference = '<?php echo $reference; ?>';
    
    // Validate form fields
    if (!address || !city || !zip) {
        alert('Please fill in all required delivery information first.');
        return;
    }
    
    // Show confirmation
    const confirmMessage = 'You are about to place an order for Le ' + total.toFixed(2) + 
                           '\nReference: ' + reference + 
                           '\n\nClick OK to proceed with Orange Money payment.';
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    button.disabled = true;
    
    // Prepare form data for submission
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);
    formData.append('payment_method', 'orange_money');
    
    // Submit form via AJAX to place order first
    fetch('<?php echo BASE_URL; ?>checkout/place-order.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Order placed successfully, now redirect to Orange Money
            alert('Order placed successfully! You will now be redirected to Orange Money.');
            window.location.href = 'tel:*144*4*1*276193#';
        } else {
            alert('Error placing order: ' + data.message);
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing your order. Please try again.');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>

<style>
.btn-warning {
    background: #ff8c00;
    border-color: #ff8c00;
    color: white;
}

.btn-warning:hover {
    background: #e67e00;
    border-color: #e67e00;
    color: white;
}

.btn-warning i {
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #ff8c00, #ff6a00);
    border: none;
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(255, 140, 0, 0.4);
    color: white;
}
</style>

<?php require_once '../includes/footer.php'; ?>