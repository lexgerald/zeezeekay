<?php
// index.php - Homepage
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$db = getDB();

// Get featured products
$featured = $db->query("SELECT * FROM products WHERE featured = 1 LIMIT 6")->fetchAll();

// Get latest products
$latest = $db->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Banner slides data (you can store these in database for dynamic management)
$slides = [
    [
        'image' => 'zeezeekay.png',
        'title' => 'Zeekay',
        'subtitle' => 'Your one-stop shop for quality products at affordable prices. (Build Tomorrow Today)',
        'btn_text' => 'Start Shopping',
        'btn_link' => BASE_URL . 'products/index.php',
        'bg_color' => 'linear-gradient(135deg, #4979e1 0%, #2225d6 100%)'
    ],
    [
        'image' => '1782393686_6a3d2b5666225.png',
        'title' => 'Monthly Sale!',
        'subtitle' => 'Get up to 50% off on selected items. Limited time offer!',
        'btn_text' => 'Shop Now',
        'btn_link' => BASE_URL . 'products/index.php',
        'bg_color' => 'linear-gradient(135deg, #ea5c0f 0%, #ecb37f 100%)'
    ],
    [
        'image' => '1782393674_6a3d2b4a9174b.png',
        'title' => 'New Arrivals',
        'subtitle' => 'Discover our latest collection of premium products.',
        'btn_text' => 'Explore',
        'btn_link' => BASE_URL . 'products/index.php',
        'bg_color' => 'linear-gradient(135deg, #4facfe 0%, #74ecf2 100%)'
    ]
];
?>

<!-- Hero Banner Slider -->
<div class="hero-slider mb-5">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <!-- Indicators -->
        <div class="carousel-indicators">
            <?php foreach($slides as $index => $slide): ?>
                <button type="button" data-bs-target="#heroCarousel" 
                        data-bs-slide-to="<?php echo $index; ?>" 
                        class="<?php echo $index === 0 ? 'active' : ''; ?>"
                        aria-label="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
        
        <!-- Slides -->
        <div class="carousel-inner">
            <?php foreach($slides as $index => $slide): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="slide-wrapper" style="background: <?php echo $slide['bg_color']; ?>;">
                        <div class="container">
                            <div class="row align-items-center min-vh-50">
                                <div class="col-lg-6 text-white slide-content">
                                    <div class="slide-text">
                                        <?php if($index === 0): ?>
                                            <div class="slide-badge mb-3">
                                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Featured</span>
                                            </div>
                                        <?php endif; ?>
                                        <h1 class="display-3 fw-bold slide-title"><?php echo $slide['title']; ?></h1>
                                        <p class="lead slide-subtitle"><?php echo $slide['subtitle']; ?></p>
                                        <a href="<?php echo $slide['btn_link']; ?>" class="btn btn-light btn-lg slide-btn">
                                            <?php echo $slide['btn_text']; ?> <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 text-center slide-image">
                                    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo $slide['image']; ?>" 
                                         alt="<?php echo $slide['title']; ?>"
                                         class="img-fluid slide-img"
                                         onerror="this.src='<?php echo BASE_URL; ?>assets/images/1782393745_6a3d2b911dede.png'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<div class="container">
    <!-- Featured Products -->
    <?php if(!empty($featured)): ?>
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-star-fill text-warning"></i> Featured Products</h2>
        <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-outline-primary">
            View All <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php foreach($featured as $product): ?>
            <div class="col">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-badge">
                        <span class="badge bg-danger"><i class="bi bi-fire"></i> Hot</span>
                    </div>
                    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="height: 200px; object-fit: contain; padding: 20px;"
                         onerror="this.src='<?php echo BASE_URL; ?>assets/images/placeholder.png'">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-truncate"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success fs-5">Le<?php echo number_format($product['price']); ?></span>
                            <div>
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-id="<?php echo $product['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-price="<?php echo $product['price']; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <a href="<?php echo BASE_URL; ?>products/product-details.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Latest Products -->
    <?php if(!empty($latest)): ?>
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history text-primary"></i> Latest Products</h2>
        <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-outline-primary">
            View All <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach($latest as $product): ?>
            <div class="col">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-badge">
                        <span class="badge bg-info"><i class="bi bi-clock"></i> New</span>
                    </div>
                    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="height: 200px; object-fit: contain; padding: 20px;"
                         onerror="this.src='<?php echo BASE_URL; ?>assets/images/placeholder.png'">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-truncate"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success fs-5">Le<?php echo number_format($product['price']); ?></span>
                            <div>
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-id="<?php echo $product['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-price="<?php echo $product['price']; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <a href="<?php echo BASE_URL; ?>products/product-details.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Newsletter Section 
    <div class="row mt-5">
        <div class="col-12">
            <div class="newsletter-section p-5 rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="row align-items-center">
                    <div class="col-md-6 text-white">
                        <h3><i class="bi bi-envelope-paper"></i> Subscribe to Our Newsletter</h3>
                        <p class="mb-0">Get the latest updates on new products and special offers.</p>
                    </div>
                    <div class="col-md-6">
                        <form class="d-flex gap-2" onsubmit="return false;">
                            <input type="email" class="form-control form-control-lg" placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-warning btn-lg">
                                Subscribe <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
</div>

<style>
/* Hero Slider Styles */
.hero-slider {
    margin-top: -1.5rem;
}

.slide-wrapper {
    min-height: 450px;
    display: flex;
    align-items: center;
    padding: 60px 0;
    position: relative;
    overflow: hidden;
}

.slide-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.1);
    z-index: 0;
}

.slide-content {
    position: relative;
    z-index: 1;
}

.slide-title {
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    animation: slideInLeft 0.8s ease;
}

.slide-subtitle {
    font-weight: 300;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    animation: slideInLeft 1s ease;
    margin-bottom: 25px;
}

.slide-btn {
    font-weight: 600;
    padding: 12px 35px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    animation: slideInLeft 1.2s ease;
}

.slide-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.3);
}

.slide-image {
    position: relative;
    z-index: 1;
}

.slide-img {
    max-height: 300px;
    filter: drop-shadow(0 10px 30px rgba(0,0,0,0.2));
    animation: slideInRight 0.8s ease;
}

.slide-badge .badge {
    font-size: 0.9rem;
    padding: 8px 16px;
    animation: pulse 2s infinite;
}

.carousel-indicators {
    bottom: 20px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    margin: 0 5px;
}

.carousel-indicators .active {
    background: white;
}

.carousel-control-prev,
.carousel-control-next {
    width: 50px;
    height: 50px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    background: rgba(255,255,255,0.4);
}

.carousel-control-prev {
    left: 20px;
}

.carousel-control-next {
    right: 20px;
}

/* Animations */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Product Cards */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
}

.product-badge .badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
}

.product-card .card-img-top {
    background: #f8f9fa;
}

.product-card .card-body {
    padding: 1.25rem;
}

.product-card .card-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Newsletter Section */
.newsletter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.newsletter-section .form-control {
    border-radius: 50px;
    border: none;
    padding: 12px 20px;
}

.newsletter-section .btn-warning {
    border-radius: 50px;
    padding: 12px 30px;
    font-weight: 600;
}

/* Section Headers */
.section-header h2 {
    font-weight: 700;
    position: relative;
}

.section-header h2::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

/* Responsive */
@media (max-width: 768px) {
    .slide-wrapper {
        min-height: 350px;
        padding: 40px 0;
    }
    
    .slide-title {
        font-size: 2rem !important;
    }
    
    .slide-subtitle {
        font-size: 1rem !important;
    }
    
    .slide-img {
        max-height: 200px;
        margin-top: 20px;
    }
    
    .slide-text {
        text-align: center;
    }
    
    .slide-btn {
        padding: 10px 25px;
        font-size: 0.9rem;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 35px;
        height: 35px;
    }
    
    .carousel-control-prev {
        left: 5px;
    }
    
    .carousel-control-next {
        right: 5px;
    }
    
    .newsletter-section {
        padding: 30px !important;
    }
    
    .newsletter-section .btn-lg {
        font-size: 1rem;
        padding: 10px 20px;
    }
}

@media (max-width: 576px) {
    .slide-wrapper {
        min-height: 300px;
        padding: 30px 0;
    }
    
    .slide-title {
        font-size: 1.5rem !important;
    }
}
</style>

<script>
// Define BASE_URL for JavaScript
const BASE_URL = '<?php echo BASE_URL; ?>';

// Add to cart functionality with enhanced features
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = this.dataset.price;
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            this.disabled = true;
            
            // Send AJAX request
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
                    // Show success toast notification
                    showToast('success', `${productName} added to cart!`);
                    
                    // Update cart count in navbar
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        let count = parseInt(cartCount.textContent) || 0;
                        cartCount.textContent = count + 1;
                    }
                } else {
                    // Show error toast notification
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
    // Remove existing toasts to avoid stacking
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    
    // Set background color based on type
    const bgColor = type === 'success' ? 'bg-success' : 
                   type === 'danger' ? 'bg-danger' : 
                   type === 'warning' ? 'bg-warning text-dark' : 'bg-info';
    
    // Set icon based on type
    const icon = type === 'success' ? 'bi-check-circle-fill' : 
                type === 'danger' ? 'bi-x-circle-fill' : 
                type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill';
    
    toast.className = `toast align-items-center ${bgColor} border-0 show`;
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.style.minWidth = '300px';
    toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${icon} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 3.5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
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
    container.style.width = '100%';
    document.body.appendChild(container);
    return container;
}

// Newsletter subscription
document.querySelector('.newsletter-section form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    if (email) {
        showToast('success', 'Thank you for subscribing! Check your email for updates.');
        this.querySelector('input[type="email"]').value = '';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>