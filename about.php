<?php
// about.php - About Us Page
require_once 'config/config.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold">About Zeekay Products</h1>
            <div class="divider mx-auto" style="width: 80px; height: 4px; background: linear-gradient(135deg, #667eea, #324eec); border-radius: 2px;"></div>
            <p class="text-muted mt-3 fs-5">Your trusted partner for quality products</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-5">
        <!-- Company Story -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-primary"><i class="bi bi-building"></i> Our Story</h3>
                    <p class="lead">Welcome to Zeekay, "Build Tomorrow Today"</p>
                    <p>
                        Zeekay motto "Build Tomorrow Today" isn't just our company motto, it's the mindset behind everything we do.
                    </p>
                    <p>
                        It means every product we offer is chosen with the future in mind; durability,
                        reliability, and long-term value. we're not here for quick sales or temporary solutions;
                        we are here to help you invest in terms that will serve you well beyond today.
                    </p>
                    <p>
                        It also reflects our commitment to growth, our service, and your experience. Because when you shop with us, you're 
                        not just buying a product... you're bulding something lasting.
                    </p>
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <span>Build smart today, enjoy peace of mind tomorrow.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-primary"><i class="bi bi-bullseye"></i> Our Mission</h3>
                    <p>
                        To provide a seamless and enjoyable shopping experience for our customers by offering 
                        quality products, competitive prices, and exceptional customer service.
                    </p>
                    
                    <h4 class="text-primary mt-4"><i class="bi bi-eye"></i> Our Vision</h4>
                    <p>
                        To become the leading e-commerce platform in Sierra Leone, known for reliability, 
                        quality, and customer trust.
                    </p>
                    
                    <h4 class="text-primary mt-4"><i class="bi bi-heart"></i> Our Values</h4>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Integrity</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Quality</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Innovation</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Customer Focus</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- What We Offer -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Why Choose Zeekay?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-box-seam" style="font-size: 2.5rem; color: #667eea;"></i>
                            </div>
                            <h5>Quality Products</h5>
                            <p class="text-muted">We source only the best products from trusted suppliers to ensure quality.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-truck" style="font-size: 2.5rem; color: #667eea;"></i>
                            </div>
                            <h5>Fast Delivery</h5>
                            <p class="text-muted">We deliver your orders quickly and efficiently to your doorstep.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-headset" style="font-size: 2.5rem; color: #667eea;"></i>
                            </div>
                            <h5>Customer Support</h5>
                            <p class="text-muted">Our dedicated support team is always ready to assist you.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4"><i class="bi bi-info-circle"></i> Get in Touch</h3>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-geo-alt" style="font-size: 2rem; color: #2225d6;"></i><br>
                            <strong>Address</strong><br>
                            <a href="#"
                                style="color: #090909; text-decoration: none; opacity: 0.85; transition: opacity 0.3s;"
                                onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                                Hamilton Junction No2 River Quarry Junction, Peninsular Hwy, Freetown
                            </a>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-envelope" style="font-size: 2rem; color: #2225d6;"></i><br>
                            <strong>Email</strong><br>
                            <a href="mailto:info@zeezeekay.com"
                                style="color: #090909; text-decoration: none; opacity: 0.85; transition: opacity 0.3s;"
                                onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                                info@zeezeekay.com
                            </a>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-phone" style="font-size: 2rem; color: #2225d6;"></i><br>
                            <strong>Contact</strong><br>
                            <a href="tel:+23299999849" 
                                style="color: #090909; text-decoration: none; opacity: 0.85; transition: opacity 0.3s;"
                                onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                                +(232) 99999849
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Working Hours -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4"><i class="bi bi-clock"></i> Working Hours</h3>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span><strong>Monday - Friday</strong></span>
                                <span>9:00 AM - 6:00 PM</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span><strong>Saturday</strong></span>
                                <span>9:00 AM - 6:00 PM</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span><strong>Sunday</strong></span>
                                <span>Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5 mb-5">
        <div class="col-12 text-center">
            <div class="p-5 rounded-3" style="background: linear-gradient(135deg, #647dee, #2225d6); color: white;">
                <h3 class="fw-bold">Ready to Shop with Us?</h3>
                <p class="lead">Explore our collection of quality products and experience the best shopping.</p>
                <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-light btn-lg">
                    <i class="bi bi-shop"></i> Browse Products
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.feature-icon {
    display: inline-block;
    padding: 15px;
    background: #f0f3ff;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.feature-icon:hover {
    transform: scale(1.1);
    background: #2225d6;
}

.feature-icon:hover i {
    color: white !important;
}

.divider {
    margin-bottom: 1rem;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.rounded-3 {
    border-radius: 20px !important;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .fs-5 {
        font-size: 1rem !important;
    }
    
    .lead {
        font-size: 1rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>