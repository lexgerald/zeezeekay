<?php
// products/product-details.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = $_GET['id'] ?? 0;
$db = getDB();
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: " . BASE_URL . "products/index.php");
    exit;
}
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="text-muted">Category: <?php echo htmlspecialchars($product['category']); ?></p>
            <h3 class="text-success">Le<?php echo number_format($product['price'], 2); ?></h3>
            <p><strong>Stock:</strong> <?php echo $product['stock'] > 0 ? $product['stock'] . ' units' : 'Out of Stock'; ?></p>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <?php if($product['stock'] > 0): ?>
                <div class="mt-4">
                    <label for="quantity" class="form-label">Quantity</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="quantity" class="form-control" style="width: 100px;" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        <button class="btn btn-primary btn-lg add-to-cart" 
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-price="<?php echo $product['price']; ?>">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">This product is currently out of stock.</div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
            </div>
        </div>
    </div>
</div>

<script>
// Define BASE_URL for JavaScript
const BASE_URL = '<?php echo BASE_URL; ?>';

// Add to cart functionality for product details page
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.querySelector('.add-to-cart');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = this.dataset.price;
            const quantity = document.getElementById('quantity')?.value || 1;
            
            // Validate quantity
            if (quantity < 1) {
                showToast('warning', 'Please select a valid quantity');
                return;
            }
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
            this.disabled = true;
            
            // Send AJAX request
            fetch(BASE_URL + 'cart/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                // Reset button
                this.innerHTML = originalText;
                this.disabled = false;
                
                if (data.success) {
                    // Show success message
                    showToast('success', `${productName} added to cart! (${quantity} items)`);
                    
                    // Update cart count in navbar
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        let count = parseInt(cartCount.textContent) || 0;
                        cartCount.textContent = count + parseInt(quantity);
                    }
                } else {
                    showToast('danger', data.message || 'Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
                showToast('danger', 'Error adding product to cart');
            });
        });
    }
});

// Toast notification function
function showToast(type, message) {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : type === 'danger' ? 'bi-x-circle-fill' : 'bi-info-circle-fill'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Create toast container if it doesn't exist
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.position = 'fixed';
    container.style.top = '80px';
    container.style.right = '20px';
    container.style.zIndex = '9999';
    container.style.maxWidth = '350px';
    document.body.appendChild(container);
    return container;
}
</script>

<?php require_once '../includes/footer.php'; ?>