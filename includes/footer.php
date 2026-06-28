<?php
// includes/footer.php
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config/config.php';
}
?>

<!-- WhatsApp Widget - Professional Redesign -->
<div id="whatsapp-widget">
    <!-- Toggle Button -->
    <div id="whatsapp-toggle" class="whatsapp-toggle">
        <div class="whatsapp-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30" fill="white">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </div>
        <span class="whatsapp-badge">1</span>
    </div>

    <!-- Chat Window -->
    <div id="whatsapp-chat" class="whatsapp-chat">
        <!-- Header -->
        <div class="whatsapp-chat-header">
            <div class="whatsapp-header-left">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="white">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <div>
                    <span class="whatsapp-header-title">Zeekay Support</span>
                    <span class="whatsapp-header-status">Online</span>
                </div>
            </div>
            <button id="whatsapp-close" class="whatsapp-close">×</button>
        </div>

        <!-- Chat Body -->
        <div class="whatsapp-chat-body">
            <div class="whatsapp-message received">
                <div class="message-content">
                    👋 Hello! How can we help you today? We're here to assist!
                </div>
                <span class="message-time">Just now</span>
            </div>
            <div class="whatsapp-typing">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>

        <!-- Chat Footer -->
        <div class="whatsapp-chat-footer">
            <a id="whatsapp-desktop" target="_blank" 
               href="https://wa.me/23299999849?text=Hello%20Zeekay%20Support%2C%20I%20came%20from%20your%20website" 
               class="whatsapp-cta-btn">
                💬 Start Chat on WhatsApp
            </a>
            <a id="whatsapp-mobile" target="_blank" 
               href="https://wa.me/23299999849" 
               class="whatsapp-cta-btn whatsapp-cta-mobile">
                💬 Chat Now
            </a>
        </div>
    </div>
</div>

<style>
/* ===== WhatsApp Widget Styles ===== */
#whatsapp-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
}

/* Toggle Button */
.whatsapp-toggle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    position: relative;
}

.whatsapp-toggle:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 6px 30px rgba(37, 211, 102, 0.5);
}

.whatsapp-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.whatsapp-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    animation: pulse-badge 2s infinite;
}

/* Chat Window */
.whatsapp-chat {
    position: absolute;
    bottom: 75px;
    right: 0;
    width: 360px;
    max-width: calc(100vw - 40px);
    background: #f0f0f0;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2);
    display: none;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.whatsapp-chat.active {
    display: block;
}

/* Chat Header */
.whatsapp-chat-header {
    background: linear-gradient(135deg, #075E54 0%, #128C7E 100%);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.whatsapp-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.whatsapp-header-left svg {
    width: 28px;
    height: 28px;
}

.whatsapp-header-title {
    color: white;
    font-weight: 600;
    font-size: 16px;
    display: block;
}

.whatsapp-header-status {
    color: rgba(255, 255, 255, 0.7);
    font-size: 12px;
    font-weight: 400;
}

.whatsapp-header-status::before {
    content: "●";
    color: #4ade80;
    margin-right: 4px;
    font-size: 10px;
}

.whatsapp-close {
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s;
    line-height: 1;
    padding: 0 4px;
}

.whatsapp-close:hover {
    opacity: 1;
}

/* Chat Body */
.whatsapp-chat-body {
    padding: 16px 20px 10px;
    min-height: 150px;
    max-height: 300px;
    overflow-y: auto;
    background: #ece5dd;
    background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48cGF0aCBkPSJNMzAgMjBhMTAgMTAgMCAxIDEtMjAgMCAxMCAxMCAwIDAgMSAyMCAweiIgZmlsbD0iI2ZmZmZmZiIgb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==');
}

.whatsapp-message {
    margin-bottom: 12px;
    display: flex;
    flex-direction: column;
}

.whatsapp-message.received {
    align-items: flex-start;
}

.whatsapp-message .message-content {
    background: white;
    padding: 10px 14px;
    border-radius: 12px 12px 12px 4px;
    max-width: 85%;
    font-size: 14px;
    line-height: 1.5;
    color: #1a1a1a;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    word-wrap: break-word;
}

.whatsapp-message .message-time {
    font-size: 10px;
    color: #999;
    margin-top: 4px;
    margin-left: 12px;
}

/* Typing Animation */
.whatsapp-typing {
    display: none;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: white;
    border-radius: 12px 12px 12px 4px;
    width: 60px;
    margin-bottom: 12px;
}

.whatsapp-typing.active {
    display: flex;
}

.whatsapp-typing .dot {
    width: 6px;
    height: 6px;
    background: #999;
    border-radius: 50%;
    animation: typingDot 1.4s infinite;
}

.whatsapp-typing .dot:nth-child(2) {
    animation-delay: 0.2s;
}

.whatsapp-typing .dot:nth-child(3) {
    animation-delay: 0.4s;
}

/* Chat Footer */
.whatsapp-chat-footer {
    padding: 12px 20px 16px;
    background: #fff;
    border-top: 1px solid #e8e8e8;
}

.whatsapp-cta-btn {
    display: block;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
}

.whatsapp-cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(37, 211, 102, 0.4);
    color: white;
}

.whatsapp-cta-mobile {
    display: none;
}

/* Animations */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse-badge {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
}

@keyframes typingDot {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-6px);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 576px) {
    #whatsapp-widget {
        bottom: 20px;
        right: 20px;
    }
    
    .whatsapp-toggle {
        width: 55px;
        height: 55px;
    }
    
    .whatsapp-chat {
        width: calc(100vw - 40px);
        bottom: 70px;
        right: 0;
    }
    
    .whatsapp-cta-desktop {
        display: none;
    }
    
    .whatsapp-cta-mobile {
        display: block;
    }
}

@media (min-width: 577px) {
    .whatsapp-cta-desktop {
        display: block;
    }
    
    .whatsapp-cta-mobile {
        display: none;
    }
}
</style>

<script>
// ===== WhatsApp Widget JavaScript =====
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('whatsapp-toggle');
    const chatWindow = document.getElementById('whatsapp-chat');
    const closeBtn = document.getElementById('whatsapp-close');
    let isOpen = false;
    
    // Open/Close chat
    function toggleChat(e) {
        e.stopPropagation();
        isOpen = !isOpen;
        chatWindow.classList.toggle('active', isOpen);
        
        // Show typing indicator after opening
        if (isOpen) {
            setTimeout(showTyping, 500);
        }
    }
    
    function showTyping() {
        const typing = document.querySelector('.whatsapp-typing');
        typing.classList.add('active');
        
        setTimeout(() => {
            typing.classList.remove('active');
            // Add second message after typing
            const body = document.querySelector('.whatsapp-chat-body');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'whatsapp-message received';
            msgDiv.innerHTML = `
                <div class="message-content">💬 We typically reply within 5 minutes. How can we assist you today?</div>
                <span class="message-time">Just now</span>
            `;
            body.appendChild(msgDiv);
            body.scrollTop = body.scrollHeight;
        }, 2000);
    }
    
    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);
    
    // Close chat when clicking outside
    document.addEventListener('click', function(e) {
        if (isOpen && !e.target.closest('#whatsapp-widget')) {
            isOpen = false;
            chatWindow.classList.remove('active');
        }
    });
});
</script>

<!-- Original Footer -->
<footer class="bg-dark text-light py-5 mt-5" style="border-top: 3px solid #f8c146;">
    <div class="container">
        <div class="row">
            <!-- Column 1: Brand -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 style="font-weight: 700; color: #f8c146; letter-spacing: 1px;">
                    <i class="bi bi-bag-fill" style="margin-right: 8px;"></i>Zeekay Store
                </h5>
                <p style="opacity: 0.7; font-size: 0.95rem; margin-top: 12px; line-height: 1.6;">
                    Your trusted online shopping destination for quality products at affordable prices.
                </p>
                <!-- Social Icons -->
                <div style="margin-top: 15px;">
                    <a href="#" style="color: #fff; opacity: 0.6; margin-right: 12px; font-size: 1.2rem; transition: opacity 0.3s;" 
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" style="color: #fff; opacity: 0.6; margin-right: 12px; font-size: 1.2rem; transition: opacity 0.3s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="#" style="color: #fff; opacity: 0.6; margin-right: 12px; font-size: 1.2rem; transition: opacity 0.3s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" style="color: #fff; opacity: 0.6; font-size: 1.2rem; transition: opacity 0.3s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 style="font-weight: 600; color: #f8c146; letter-spacing: 0.5px; margin-bottom: 18px;">
                    <i class="bi bi-link-45deg" style="margin-right: 8px;"></i>Quick Links
                </h5>
                <ul class="list-unstyled" style="line-height: 2.2;">
                    <li>
                        <a href="<?php echo BASE_URL; ?>products/index.php" 
                           style="color: #fff; text-decoration: none; opacity: 0.75; transition: all 0.3s;"
                           onmouseover="this.style.opacity='1'; this.style.paddingLeft='8px';" 
                           onmouseout="this.style.opacity='0.75'; this.style.paddingLeft='0';">
                            <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i> Products
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>cart/cart.php" 
                           style="color: #fff; text-decoration: none; opacity: 0.75; transition: all 0.3s;"
                           onmouseover="this.style.opacity='1'; this.style.paddingLeft='8px';" 
                           onmouseout="this.style.opacity='0.75'; this.style.paddingLeft='0';">
                            <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i> Cart
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>orders/orders.php" 
                           style="color: #fff; text-decoration: none; opacity: 0.75; transition: all 0.3s;"
                           onmouseover="this.style.opacity='1'; this.style.paddingLeft='8px';" 
                           onmouseout="this.style.opacity='0.75'; this.style.paddingLeft='0';">
                            <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i> My Orders
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="#" 
                           style="color: #fff; text-decoration: none; opacity: 0.75; transition: all 0.3s;"
                           onmouseover="this.style.opacity='1'; this.style.paddingLeft='8px';" 
                           onmouseout="this.style.opacity='0.75'; this.style.paddingLeft='0';">
                            <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i> FAQ
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Contact -->
            <div class="col-md-4">
                <h5 style="font-weight: 600; color: #f8c146; letter-spacing: 0.5px; margin-bottom: 18px;">
                    <i class="bi bi-headset" style="margin-right: 8px;"></i>Contact Us
                </h5>
                <div style="margin-bottom: 12px;">
                    <i class="bi bi-envelope-fill" style="color: #f8c146; margin-right: 10px;"></i>
                    <a href="mailto:info@zeekay.com" 
                       style="color: #fff; text-decoration: none; opacity: 0.85; transition: opacity 0.3s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                        info@zeekay.com
                    </a>
                </div>
                <div style="margin-bottom: 12px;">
                    <i class="bi bi-telephone-fill" style="color: #f8c146; margin-right: 10px;"></i>
                    <a href="tel:+23299999849" 
                       style="color: #fff; text-decoration: none; opacity: 0.85; transition: opacity 0.3s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                        +(232) 99999849
                    </a>
                </div>
                <div>
                    <i class="bi bi-geo-alt-fill" style="color: #f8c146; margin-right: 10px;"></i>
                    <span style="opacity: 0.7; font-size: 0.95rem;">123 Commerce Ave, Suite 100<br>New York, NY 10001</span>
                </div>
            </div>
        </div>

        <!-- Divider with decorative line -->
        <hr style="border-color: rgba(255,255,255,0.1); margin: 30px 0 20px;">

        <!-- Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small style="opacity: 0.6;">
                    &copy; <?php echo date('Y'); ?> <strong style="color: #f8c146;">Zeekay</strong>. All rights reserved.
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small style="opacity: 0.4; font-size: 0.75rem;">
                    <i class="bi bi-shield-check"></i> Secure Shopping &bull; 
                    <i class="bi bi-credit-card"></i> SSL Encrypted
                </small>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/cart.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/checkout.js"></script>
</body>
</html>