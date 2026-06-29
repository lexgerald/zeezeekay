<?php
// faq.php - Frequently Asked Questions
require_once 'config/config.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold">Frequently Asked Questions</h1>
            <div class="divider mx-auto" style="width: 80px; height: 4px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px;"></div>
            <p class="text-muted mt-3 fs-5">Find answers to the most commonly asked questions</p>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button class="btn btn-primary btn-sm filter-btn active" data-filter="all">All Questions</button>
                <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="orders">Orders</button>
                <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="payments">Payments</button>
                <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="shipping">Delivery</button>
                <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="returns">Returns</button>
                <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="account">Account</button>
            </div>
        </div>
    </div>

    <!-- FAQ Accordion -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="accordion" id="faqAccordion">
                
                <!-- Category: Orders -->
                <div class="faq-category" data-category="orders">
                    <h3 class="mb-3 text-primary"><i class="bi bi-box-seam"></i> Orders</h3>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                How do I place an order?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                To place an order, you first need to create an account and login to simply browse our products, click "Add to Cart" on any item you wish to purchase. 
                                When you're ready, go to your cart, review your items, and proceed to checkout. Fill in your delivery
                                details and choose your preferred payment method to complete your order.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                Can I modify or cancel my order after placing it?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Orders can be modified or cancelled within 24 hours of placement. Please contact our customer support 
                                team immediately at support@zeezeekay.com with your order number. After 24 hours, orders are processed 
                                and cannot be modified.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                How do I track my order?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once your order is confirmed and delivered, you will receive a tracking number via whatsap. 
                                You can also track your order by logging into your account and visiting the "My Orders" section.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category: Payments -->
                <div class="faq-category mt-4" data-category="payments">
                    <h3 class="mb-3 text-success"><i class="bi bi-credit-card"></i> Payments</h3>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                <i class="bi bi-question-circle text-success me-2"></i>
                                What payment methods do you accept?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We accept the following payment methods:
                                <ul class="mb-0">
                                    <li><strong>Orange Money</strong> - Pay via mobile money</li>
                                    <li><strong>Cash on Delivery</strong> - Pay when you receive your order</li>
                                    <li><strong>Bank Transfer</strong> - Direct bank transfers (contact us for details)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                <i class="bi bi-question-circle text-success me-2"></i>
                                Is my payment information secure?
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! We take security very seriously. All payment transactions are encrypted and processed through 
                                secure channels. We never store your payment information on our servers.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq6">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                <i class="bi bi-question-circle text-success me-2"></i>
                                What is the Orange Money payment process?
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="faq6" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                To pay with Orange Money:
                                <ol class="mb-0">
                                    <li>Select "Pay with Orange Money" at checkout</li>
                                    <li>You'll receive a reference number</li>
                                    <li>Dial *144# on your Orange phone</li>
                                    <li>Select "Pay Merchant" and enter the payment details</li>
                                    <li>Confirm the transaction with your PIN</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category: Shipping -->
                <div class="faq-category mt-4" data-category="shipping">
                    <h3 class="mb-3 text-info"><i class="bi bi-truck"></i> Delivery</h3>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq7">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                <i class="bi bi-question-circle text-info me-2"></i>
                                What are your delivery fees?
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="faq7" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer <strong>free delivery</strong> on all orders! There are no hidden fees or additional 
                                charges for delivery. Your order will be delivered to your doorstep at no extra cost.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq8">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                <i class="bi bi-question-circle text-info me-2"></i>
                                How long does delivery take?
                            </button>
                        </h2>
                        <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="faq8" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Delivery times vary depending on your location:
                                <ul class="mb-0">
                                    <li><strong>Freetown:</strong> 1-3 business days</li>
                                    <li><strong>Other cities:</strong> 3-7 business days</li>
                                    <li><strong>Remote areas:</strong> 7-14 business days</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq9">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                                <i class="bi bi-question-circle text-info me-2"></i>
                                Do you deliver to my area?
                            </button>
                        </h2>
                        <div id="collapse9" class="accordion-collapse collapse" aria-labelledby="faq9" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We deliver across Sierra Leone! If you're unsure about delivery to your specific location, 
                                please contact our customer support team and we'll be happy to assist you.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category: Returns -->
                <div class="faq-category mt-4" data-category="returns">
                    <h3 class="mb-3 text-danger"><i class="bi bi-arrow-return-left"></i> Returns & Refunds</h3>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq10">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                <i class="bi bi-question-circle text-danger me-2"></i>
                                What is your return policy?
                            </button>
                        </h2>
                        <div id="collapse10" class="accordion-collapse collapse" aria-labelledby="faq10" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We accept returns within 7 days of delivery for unused items in their original packaging. 
                                Please contact our customer support team to initiate a return. Return shipping fees may apply.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq11">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                <i class="bi bi-question-circle text-danger me-2"></i>
                                How do I get a refund?
                            </button>
                        </h2>
                        <div id="collapse11" class="accordion-collapse collapse" aria-labelledby="faq11" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once your return is received and inspected, we will notify you of the approval or rejection 
                                of your refund. If approved, the refund will be processed to your original payment method 
                                within 5-10 business days.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category: Account -->
                <div class="faq-category mt-4" data-category="account">
                    <h3 class="mb-3 text-warning"><i class="bi bi-person"></i> Account</h3>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq12">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse12" aria-expanded="false" aria-controls="collapse12">
                                <i class="bi bi-question-circle text-warning me-2"></i>
                                How do I create an account?
                            </button>
                        </h2>
                        <div id="collapse12" class="accordion-collapse collapse" aria-labelledby="faq12" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Click on the "Register" button at the top of the page. Fill in your name, email address, 
                                and password. Once registered, You can then log in 
                                and start shopping!
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq13">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse13" aria-expanded="false" aria-controls="collapse13">
                                <i class="bi bi-question-circle text-warning me-2"></i>
                                I forgot my password. What should I do?
                            </button>
                        </h2>
                        <div id="collapse13" class="accordion-collapse collapse" aria-labelledby="faq13" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                On the login page, click on "Forgot Password". Enter your registered email address and 
                                we'll send you a link to reset your password. If you don't receive the email, please 
                                check your spam folder.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq14">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse14" aria-expanded="false" aria-controls="collapse14">
                                <i class="bi bi-question-circle text-warning me-2"></i>
                                How do I update my account information?
                            </button>
                        </h2>
                        <div id="collapse14" class="accordion-collapse collapse" aria-labelledby="faq14" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Log in to your account and go to "Account Settings" or "Profile". From there, you can 
                                update your personal information, change your password, and manage your preferences.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Still Have Questions -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea, #213bce);">
                <div class="card-body p-5 text-center text-white">
                    <h3 class="fw-bold"><i class="bi bi-chat-dots"></i> Still Have Questions?</h3>
                    <p class="mb-4">We're here to help! Contact our support team for personalized assistance.</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="mailto:support@zeezeekay.com" class="btn btn-light">
                            <i class="bi bi-envelope"></i> Email Us
                        </a>
                        <a href="tel:+23299999849" class="btn btn-outline-light">
                            <i class="bi bi-phone"></i> Call Us
                        </a>
                        <a href="<?php echo BASE_URL; ?>about.php" class="btn btn-outline-light">
                            <i class="bi bi-info-circle"></i> About Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.accordion-item {
    border: none;
    margin-bottom: 12px;
    background: white;
    border-radius: 12px !important;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
}

.accordion-item:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.accordion-button {
    font-weight: 600;
    color: #2d3748;
    padding: 18px 24px;
    background: white;
    border: none;
    border-radius: 12px !important;
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    color: #667eea;
    background: #f8f9ff;
    box-shadow: none;
    border-radius: 12px 12px 0 0 !important;
}

.accordion-button:focus {
    border-color: transparent;
    box-shadow: none;
}

.accordion-button::after {
    background-size: 1.2rem;
    color: #667eea;
}

.accordion-body {
    padding: 20px 24px 24px;
    background: #f8f9ff;
    color: #4a5568;
    line-height: 1.7;
}

.accordion-body ul, .accordion-body ol {
    padding-left: 20px;
    margin-top: 10px;
}

.accordion-body ul li, .accordion-body ol li {
    margin-bottom: 6px;
}

.faq-category h3 {
    font-size: 1.3rem;
    font-weight: 700;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.filter-btn {
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    transform: translateY(-2px);
}

.filter-btn.active {
    background: linear-gradient(135deg, #768cee, #3055dd);
    border-color: #667eea;
    color: white;
}

.divider {
    margin: 0 auto;
}

@media (max-width: 768px) {
    .accordion-button {
        font-size: 0.95rem;
        padding: 14px 18px;
    }
    
    .accordion-body {
        font-size: 0.9rem;
        padding: 16px 18px 18px;
    }
    
    .faq-category h3 {
        font-size: 1.1rem;
    }
    
    .card-body {
        padding: 30px 20px !important;
    }
}
</style>

<script>
// FAQ Category Filter
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const faqCategories = document.querySelectorAll('.faq-category');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            faqCategories.forEach(category => {
                if (filter === 'all' || category.dataset.category === filter) {
                    category.style.display = 'block';
                } else {
                    category.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>