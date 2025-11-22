<?php
/*
 * Main Router for Pinnah's Pretty Pieces
 * Handles URL params (?page=home), includes pages dynamically.
 * Integrates config, session, functions. Error-safe with fallbacks.
 */

// Enable error display for dev (remove in prod)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start output buffering to catch errors
ob_start();

// Load core files safely
$rootDir = __DIR__;
try {
    require_once $rootDir . '/config/config.php';
    require_once $rootDir . '/includes/session.php';  // Loads functions/database too
} catch (Exception $e) {
    die('Core load failed: ' . $e->getMessage() . '. Check paths/DB.');
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    logoutUser();
}

// Simple routing (supports ?page=*, ?search= for collections)
$page = sanitizeInput($_GET['page'] ?? 'home');
$search = sanitizeInput($_GET['search'] ?? '');  // For product search

// Dynamic page title
$pageTitle = ucfirst(str_replace('-', ' ', $page)) . ' - ' . SITE_NAME;

// Build page path
$pageFile = $rootDir . '/pages/' . $page . '.php';

// Include header (sets <html>, nav, etc.)
include $rootDir . '/includes/header.php';

// Route & include page (fallback to home if not exists)
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback: Home
    $pageTitle = 'Home - ' . SITE_NAME;
    include $rootDir . '/pages/home.php';
}

// Include footer (closes </body></html>)
include $rootDir . '/includes/footer.php';

// End buffer & flush
ob_end_flush();
?>