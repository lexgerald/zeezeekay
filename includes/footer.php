<?php
// includes/footer.php
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config/config.php';
}
?>
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