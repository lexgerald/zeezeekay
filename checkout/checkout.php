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
?>
<div class="container mt-4">
    <h2>Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5>Shipping Information</h5>
                    <form method="POST" action="<?php echo BASE_URL; ?>checkout/place-order.php">
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
                                <input type="text" class="form-control" id="zip" name="zip" placeholder="Enter phone number" required>
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
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-check-circle"></i> Place Order
                        </button>
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
                    <div class="alert alert-info">
                        <small><i class="bi bi-shield-check"></i> Your order is secure.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>