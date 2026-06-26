<?php
// cart/cart.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
$db = getDB();

// Get product details for cart items
$cartItems = [];
foreach($cart as $productId => $quantity) {
    $stmt = $db->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if ($product) {
        $product['quantity'] = $quantity;
        $product['subtotal'] = $product['price'] * $quantity;
        $cartItems[] = $product;
        $total += $product['subtotal'];
    }
}
?>
<div class="container mt-4">
    <h2>Shopping Cart</h2>
    
    <?php if(empty($cartItems)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="<?php echo BASE_URL; ?>products/index.php">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                             style="width: 50px; height: 50px; object-fit: contain;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </td>
                                    <td>Le<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="<?php echo BASE_URL; ?>cart/update.php" class="d-flex gap-2">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" class="form-control" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    </td>
                                    <td>Le<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>cart/remove.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to remove this item?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>Le<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Delivery:</span>
                            <span>Le0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span>Le<?php echo number_format($total, 2); ?></span>
                        </div>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo BASE_URL; ?>checkout/checkout.php" class="btn btn-primary w-100 mt-3">
                                Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>auth/login.php?redirect=<?php echo urlencode('/checkout/checkout.php'); ?>" 
                               class="btn btn-primary w-100 mt-3">
                                Login to Checkout
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-outline-secondary w-100 mt-2">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>