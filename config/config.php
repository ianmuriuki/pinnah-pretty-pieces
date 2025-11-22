<?php
/*
 * Configuration file for Pinnah's Pretty Pieces
 * Centralized settings and constants. Include this in all PHP files.
 * In production: Move sensitive values to .env and load with parse_ini_file().
 */

// Database Configuration (override in database.php if needed)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pinnahs_pretty_pieces');

// Site Settings
define('SITE_NAME', 'Pinnah\'s Pretty Pieces');
define('SITE_URL', 'http://localhost/pinnahs-pretty-pieces/');
define('ADMIN_EMAIL', 'hello@penguinscollections.com');
define('OWNER_PHONE', '+15551234567');
define('OWNER_ADDRESS', '123 Coastal Dr, Seaside City SC 12345');
define('BUSINESS_HOURS', 'Mon-Fri 9AM-6PM EST');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_NAME', 'pinnahs_session');

// File Upload Configuration (for product images, custom sketches)
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/pinnahs-pretty-pieces/uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Admin Credentials (simple for dev; hash and DB-store in prod)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('admin', PASSWORD_DEFAULT)); // Use password_verify() in auth

// Email Configuration (uses PHP mail() by default; enable SMTP for prod)
define('SMTP_ENABLED', false); // Set to true if using PHPMailer (include lib in includes/)
define('MAIL_FROM', ADMIN_EMAIL);
define('MAIL_FROM_NAME', SITE_NAME);
define('SMTP_HOST', 'smtp.gmail.com'); // Example for Gmail
define('SMTP_PORT', 587);
define('SMTP_USER', ADMIN_EMAIL);
define('SMTP_PASS', 'your_app_password'); // Use app password

// Features
define('ENABLE_CUSTOM_REQUESTS', true);
define('ENABLE_WISHLIST', true);
define('ENABLE_GUEST_CART', true); // Session-based cart for non-logged users

// CORS & Security (for API endpoints)
define('ALLOWED_ORIGINS', ['http://localhost', 'http://localhost:3000', 'http://localhost/pinnahs-pretty-pieces']);
define('API_KEY', 'pinnahs_dev_key_123'); // For future API auth; optional

// Error Handling (dev mode)
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load in other files: require_once __DIR__ . '/../config/config.php';
?>