<?php
/*
 * Configuration file for Pinnah's Pretty Pieces
 */

// 1. Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pinnahs_pretty_pieces');

// 2. Site Settings
define('SITE_NAME', "Pinnah's Pretty Pieces");
define('SITE_URL', 'http://localhost/pinnahs-pretty-pieces/');
define('ADMIN_EMAIL', 'philipinahmkamburi@gmail.com');
define('OWNER_PHONE', '+254745860767');

// 3. Session & Security Configuration
define('SESSION_TIMEOUT', 3600); 
define('SESSION_NAME', 'pinnahs_session');
define('ALLOWED_ORIGINS', ['http://localhost', 'http://localhost/pinnahs-pretty-pieces']);


// 4. File Upload Configuration
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');
define('MAX_UPLOAD_SIZE', 5242880); 
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// 5. Features
define('ENABLE_CUSTOM_REQUESTS', true);
define('ENABLE_WISHLIST', true);
define('ENABLE_GUEST_CART', true);

// 6. Error Handling (Development Mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>