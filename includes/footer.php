</main>

 
    <footer class="bg-white text-gray-700 pt-16 pb-8 border-t border-pink-200 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-gray-200 pb-10 mb-8">
                
                <!-- Column 1: Brand & Social -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl font-extrabold text-pink-600 mb-3 tracking-wider">
                        <?php echo defined('SITE_NAME') ? SITE_NAME : 'Coral Sunset'; ?>
                    </h3>
                    <p class="text-l text-gray-600 mb-6 max-w-sm">
                        Handcrafted jewelry where vibrant beads meet timeless artistry. We create treasures that capture the golden hour.
                    </p>
                    <div class="flex space-x-4 text-pink-600">
                        <!-- Placeholder Icons for Social Media - Use dark color on light background -->
                        <a href="#" class="hover:text-pink-500 transition" aria-label="Facebook"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M13.397 20.925h-2.193v-7.14h-2.11v-2.73h2.11v-2.008c0-2.072 1.267-3.2 3.106-3.2 0.887 0 1.646.066 1.872.096v2.433h-1.442c-1.13 0-1.349 0.536-1.349 1.328v1.758h2.7l-0.35 2.73h-2.35v7.14z"/></svg></a>
                        <a href="#" class="hover:text-pink-500 transition" aria-label="Instagram"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 17c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zm-3.5-7a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0zm10-5a1 1 0 1 1 2 0 1 1 0 0 1-2 0z"/></svg></a>
                        <a href="#" class="hover:text-pink-500 transition" aria-label="Twitter"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-0.81 0.36-1.68 0.61-2.59 0.72 0.94-0.56 1.66-1.45 2-2.51-0.88 0.52-1.84 0.9-2.86 1.1-0.83-0.88-2.01-1.43-3.32-1.43-2.5 0-4.54 2.04-4.54 4.54 0 0.35 0.04 0.69 0.11 1.02-3.78-0.19-7.12-2-9.36-4.76-0.39 0.67-0.61 1.44-0.61 2.27 0 1.57 0.8 2.96 2.02 3.77-0.74-0.02-1.44-0.23-2.05-0.57 0 0.02 0 0.04 0 0.06 0 2.2 1.56 4.04 3.63 4.46-0.38 0.1-0.78 0.15-1.19 0.15-0.29 0-0.57-0.03-0.84-0.08 0.57 1.8 2.23 3.1 4.19 3.14-1.55 1.2-3.5 1.92-5.64 1.92-0.37 0-0.73-0.02-1.08-0.06 2 1.28 4.38 2.02 6.95 2.02 8.35 0 12.92-6.93 12.92-12.92 0-0.19 0-0.38-0.01-0.57 0.84-0.61 1.56-1.37 2.14-2.25z"/></svg></a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="?page=home" class="text-gray-600 hover:text-pink-600 transition">Home</a></li>
                        <li><a href="?page=collections" class="text-gray-600 hover:text-pink-600 transition">Shop All</a></li>
                        <li><a href="?page=custom-design" class="text-gray-600 hover:text-pink-600 transition">Custom Designs</a></li>
                        <li><a href="?page=faq" class="text-gray-600 hover:text-pink-600 transition">FAQ / Help</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact & Support -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Get In Touch</h4>
                    <div class="space-y-3 text-sm">
                        <!-- Phone -->
                        <p>
                            <span class="font-medium text-pink-600">Call:</span> 
                            <a href="tel:<?php echo str_replace('+', '', defined('OWNER_PHONE') ? OWNER_PHONE : '+254712345678'); ?>" class="text-gray-600 hover:text-pink-600 transition">
                                <?php echo defined('OWNER_PHONE') ? OWNER_PHONE : '+254 712 345 678'; ?>
                            </a>
                        </p>
                        <!-- Email -->
                        <p>
                            <span class="font-medium text-pink-600">Email:</span> 
                            <a href="mailto:<?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'support@coralsunset.com'; ?>" class="text-gray-600 hover:text-pink-600 transition">
                                <?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'support@coralsunset.com'; ?>
                            </a>
                        </p>
                   
                    </div>
                </div>
                
             
            </div>

            <!-- Bottom Copyright and Legal Links -->
            <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-gray-600">
                <p class="mb-3 sm:mb-0">
                    &copy; <?php echo date('Y'); ?> <?php echo defined('SITE_NAME') ? SITE_NAME : 'Coral Sunset'; ?>. All rights reserved. Luxurious Beads Jewelry.
                </p>
                <div class="flex space-x-4">
                    <a href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>privacy.php" class="hover:text-pink-600 transition">Privacy Policy</a> 
                    <span class="text-gray-300">|</span>
                    <a href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>terms.php" class="hover:text-pink-600 transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- END MODERN FOOTER -->

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