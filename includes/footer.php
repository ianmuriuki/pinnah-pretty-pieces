</main>

    <footer class="site-footer">
        <div class="container">
            <div class="row footer-content">
                <!-- Column 1: Brand & Social -->
                <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-brand">
                        <?php echo defined('SITE_NAME') ? SITE_NAME : 'Pinnah\'s Pretty Pieces'; ?>
                    </h3>
                    <p class="footer-description">
                        Handcrafted jewelry where vibrant beads meet timeless artistry. We create treasures that capture the golden hour.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-2 col-md-3 mb-4 mb-md-0">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php?page=collections">Shop All</a></li>
                        <li><a href="index.php?page=custom-design">Custom Designs</a></li>
                        <li><a href="index.php?page=contact">Contact</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact & Support -->
                <div class="col-lg-2 col-md-3 mb-4 mb-md-0">
                    <h4 class="footer-heading">Get In Touch</h4>
                    <div class="footer-contact">
                        <p class="contact-item">
                            <span class="contact-label">Call:</span>
                            <a href="tel:<?php echo str_replace('+', '', defined('OWNER_PHONE') ? OWNER_PHONE : '+254712345678'); ?>">
                                <?php echo defined('OWNER_PHONE') ? OWNER_PHONE : '+254 712 345 678'; ?>
                            </a>
                        </p>
                        <p class="contact-item">
                            <span class="contact-label">Email:</span>
                            <a href="mailto:<?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'support@pinnahsprettypieces.com'; ?>">
                                <?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'support@pinnahsprettypieces.com'; ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright and Legal Links -->
            <div class="row footer-bottom">
                <div class="col-12">
                    <div class="footer-legal">
                        <p class="copyright">
                            &copy; <?php echo date('Y'); ?> <?php echo defined('SITE_NAME') ? SITE_NAME : 'Pinnah\'s Pretty Pieces'; ?>. All rights reserved.
                        </p>
                        <div class="legal-links">
                            <a href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>privacy.php">Privacy Policy</a>
                            <span class="separator">|</span>
                            <a href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>terms.php">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php 
    // Assuming isLoggedIn() is defined in the functions.php file or similar included before this footer.
    if (!function_exists('isLoggedIn')) {
        function isLoggedIn() { return false; } 
    }
    if (!isLoggedIn()): ?>
    <!-- Optional: Login modal or scripts for guests -->
    <?php endif; ?>

    <script>
        // Inline WhatsApp helper
        function openWhatsApp(message) {
            // Use fallback phone number if constant is not defined
            const phone = '<?php echo str_replace('+', '', defined('OWNER_PHONE') ? OWNER_PHONE : '+254712345678'); ?>';
            
            // Use SITE_NAME constant for the default message if available
            const defaultMessage = `Hello from <?php echo defined('SITE_NAME') ? SITE_NAME : 'Coral Sunset'; ?>! I have a question about...`;

            const url = `https://wa.me/${phone}?text=${encodeURIComponent(message || defaultMessage)}`;
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    </script>
</body>
</html>
<?php
// Clear any output buffers if needed
ob_end_flush();
?>