// assets/js/checkout.js - Checkout Functions
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    document.querySelectorAll('.payment-method-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.querySelectorAll('.payment-method-card').forEach(function(c) {
                c.classList.remove('selected');
            });
            this.classList.add('selected');
            document.getElementById('payment_method').value = this.dataset.method;
        });
    });
    
    // Form validation
    var checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (!validateCheckoutForm(this)) {
                e.preventDefault();
            }
        });
    }
    
    // Auto-calculate total
    updateOrderTotal();
});

// Validate checkout form
function validateCheckoutForm(form) {
    var isValid = true;
    var requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Validate email
    var email = form.querySelector('input[type="email"]');
    if (email && email.value && !isValidEmail(email.value)) {
        email.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate phone
    var phone = form.querySelector('input[name="phone"]');
    if (phone && phone.value && !isValidPhone(phone.value)) {
        phone.classList.add('is-invalid');
        isValid = false;
    }
    
    return isValid;
}

// Email validation
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Phone validation
function isValidPhone(phone) {
    return /^[\d\s\-+()]{10,15}$/.test(phone);
}

// Update order total
function updateOrderTotal() {
    var totalElement = document.getElementById('orderTotal');
    if (totalElement) {
        var cartTotal = getCartTotal();
        var shipping = parseFloat(document.getElementById('shippingCost')?.value) || 0;
        var tax = cartTotal * 0.08; // 8% tax
        var grandTotal = cartTotal + shipping + tax;
        
        document.getElementById('subtotal').textContent = formatCurrency(cartTotal);
        document.getElementById('shippingDisplay').textContent = formatCurrency(shipping);
        document.getElementById('taxDisplay').textContent = formatCurrency(tax);
        document.getElementById('grandTotal').textContent = formatCurrency(grandTotal);
        
        // Set hidden input for total
        var totalInput = document.getElementById('totalAmount');
        if (totalInput) {
            totalInput.value = grandTotal.toFixed(2);
        }
    }
}