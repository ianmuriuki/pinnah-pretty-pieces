    </main>
    <footer>
        <div style="text-align: center; padding: 1rem;">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. Luxurious Beads Jewelry.</p>
            <p>
                📞 <a href="tel:<?php echo str_replace('+', '', OWNER_PHONE); ?>"><?php echo OWNER_PHONE; ?></a> | 
                ✉️ <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a> | 
                💬 <a href="javascript:openWhatsApp('Hello from Pinnah\'s Pretty Pieces! I have a question about...')" style="color: inherit;">WhatsApp Support</a>
            </p>
            <p>📍 <?php echo OWNER_ADDRESS; ?> | <?php echo BUSINESS_HOURS; ?></p>
            <p style="font-size: 0.8rem; opacity: 0.7;">
                <a href="<?php echo SITE_URL; ?>privacy.php">Privacy Policy</a> | 
                <a href="<?php echo SITE_URL; ?>terms.php">Terms of Service</a>
            </p>
        </div>
    </footer>

    <?php if (!isLoggedIn()): ?>
    <!-- Optional: Login modal or scripts for guests -->
    <?php endif; ?>

    <script>
        // Inline WhatsApp helper if not in main.js
        function openWhatsApp(message) {
            const phone = '<?php echo str_replace('+', '', OWNER_PHONE); ?>';
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    </script>
</body>
</html>
<?php
// Clear any output buffers if needed
ob_end_flush();
?>