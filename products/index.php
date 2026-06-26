<?php
// products/index.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$db = getDB();
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories for filter
$catStmt = $db->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
$categories = $catStmt->fetchAll();
?>
<div class="container mt-4">
    <h2>Our Products</h2>
    
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <select name="category" class="form-select me-2">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                            <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-secondary">Filter</button>
            </form>
        </div>
    </div>
    
    <!-- Product Grid -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm product-card">
                        <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"
                             style="height: 200px; object-fit: contain;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-truncate"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="fw-bold text-success">Le<?php echo number_format($product['price'], 2); ?></p>
                            <?php if($product['stock'] > 0): ?>
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-id="<?php echo $product['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-price="<?php echo $product['price']; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Out of Stock</button>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>products/product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No products found</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Fix: Define BASE_URL for JavaScript
const BASE_URL = '<?php echo BASE_URL; ?>';

// Add to cart functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = this.dataset.price;
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
            this.disabled = true;
            
            fetch(BASE_URL + 'cart/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                // Reset button
                this.innerHTML = originalText;
                this.disabled = false;
                
                if (data.success) {
                    // Show success message
                    showToast('success', `${productName} added to cart!`);
                    
                    // Update cart count
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        let count = parseInt(cartCount.textContent) || 0;
                        cartCount.textContent = count + 1;
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
    });
});

// Toast notification function
function showToast(type, message) {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
    toast.role = 'alert';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
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