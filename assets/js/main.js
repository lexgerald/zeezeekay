// assets/js/main.js - Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Quantity input validation
    document.querySelectorAll('input[type="number"][min]').forEach(function(input) {
        input.addEventListener('change', function() {
            var min = parseInt(this.getAttribute('min')) || 0;
            if (parseInt(this.value) < min) {
                this.value = min;
            }
        });
    });
});

// Utility function to format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Utility function to get cart count
function getCartCount() {
    try {
        var cart = JSON.parse(localStorage.getItem('zeekay_cart') || '[]');
        return cart.reduce(function(sum, item) {
            return sum + (item.quantity || 0);
        }, 0);
    } catch(e) {
        return 0;
    }
}

// Update cart badge
function updateCartBadge() {
    var badge = document.getElementById('cartCount');
    if (badge) {
        badge.textContent = getCartCount();
    }
}

// Add to cart function (for non-AJAX fallback)
function addToCart(productId, quantity) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/cart/add-to-cart.php';
    
    var idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'product_id';
    idInput.value = productId;
    form.appendChild(idInput);
    
    var qtyInput = document.createElement('input');
    qtyInput.type = 'hidden';
    qtyInput.name = 'quantity';
    qtyInput.value = quantity || 1;
    form.appendChild(qtyInput);
    
    document.body.appendChild(form);
    form.submit();
}