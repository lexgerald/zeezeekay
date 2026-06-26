// assets/js/cart.js - Cart Management
document.addEventListener('DOMContentLoaded', function() {
    // Update cart badge on page load
    updateCartBadge();
    
    // Handle remove from cart (for localStorage cart)
    document.querySelectorAll('.remove-from-cart').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.dataset.id;
            removeFromCart(productId);
        });
    });
    
    // Handle quantity update
    document.querySelectorAll('.update-quantity').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var productId = this.querySelector('input[name="product_id"]').value;
            var quantity = this.querySelector('input[name="quantity"]').value;
            updateCartQuantity(productId, quantity);
        });
    });
});

// Add to cart (localStorage version)
function addToCart(productId, name, price, quantity) {
    var cart = JSON.parse(localStorage.getItem('zeekay_cart') || '[]');
    var existing = cart.find(function(item) {
        return item.id == productId;
    });
    
    if (existing) {
        existing.quantity += parseInt(quantity) || 1;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: parseFloat(price),
            quantity: parseInt(quantity) || 1
        });
    }
    
    localStorage.setItem('zeekay_cart', JSON.stringify(cart));
    updateCartBadge();
    return cart;
}

// Remove from cart
function removeFromCart(productId) {
    var cart = JSON.parse(localStorage.getItem('zeekay_cart') || '[]');
    cart = cart.filter(function(item) {
        return item.id != productId;
    });
    localStorage.setItem('zeekay_cart', JSON.stringify(cart));
    updateCartBadge();
    location.reload();
    return cart;
}

// Update cart quantity
function updateCartQuantity(productId, quantity) {
    var cart = JSON.parse(localStorage.getItem('zeekay_cart') || '[]');
    var item = cart.find(function(item) {
        return item.id == productId;
    });
    
    if (item) {
        if (parseInt(quantity) > 0) {
            item.quantity = parseInt(quantity);
        } else {
            cart = cart.filter(function(item) {
                return item.id != productId;
            });
        }
    }
    
    localStorage.setItem('zeekay_cart', JSON.stringify(cart));
    updateCartBadge();
    location.reload();
    return cart;
}

// Get cart total
function getCartTotal() {
    var cart = JSON.parse(localStorage.getItem('zeekay_cart') || '[]');
    return cart.reduce(function(total, item) {
        return total + (item.price * item.quantity);
    }, 0);
}

// Clear cart
function clearCart() {
    localStorage.removeItem('zeekay_cart');
    updateCartBadge();
    location.reload();
    return [];
}