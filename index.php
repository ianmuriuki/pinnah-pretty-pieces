<?php
// DEV: Errors/log (silent on page)
ini_set('display_errors', 0);  // Off for clean UI (logs only)
error_reporting(E_ALL);
$logFile = __DIR__ . '/debug.log';
ini_set('log_errors', 1);
ini_set('error_log', $logFile);
header('Content-Type: text/html; charset=UTF-8');

// Root dir
$rootDir = __DIR__;

// Load core
try {
    require_once $rootDir . '/config/config.php';
    error_log('DEBUG: Config OK');
    require_once $rootDir . '/config/database.php';
    error_log('DEBUG: Database OK');
    require_once $rootDir . '/includes/functions.php';
    error_log('DEBUG: Functions OK');
    require_once $rootDir . '/includes/session.php';
    error_log('DEBUG: Session OK');
} catch (Exception $e) {
    error_log('DEBUG: Core fail: ' . $e->getMessage());
    http_response_code(500);
    die('<h1 style="color: red; text-align: center;">Setup Error</h1><p>Check debug.log.</p>');
}

// Logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    logoutUser();
    error_log('DEBUG: Logout');
}

// Routing
$page = sanitizeInput($_GET['page'] ?? 'home');
$search = sanitizeInput($_GET['search'] ?? '');
$pageTitle = ucfirst(str_replace('-', ' ', $page)) . ' - ' . SITE_NAME;
$pageFile = $rootDir . '/pages/' . $page . '.php';

error_log('DEBUG: Route to ' . $page);

// Header
include $rootDir . '/includes/header.php';

// Page
if (file_exists($pageFile)) {
    include $pageFile;
    error_log('DEBUG: Page ' . $page . ' loaded');
} else {
    error_log('DEBUG: Fallback home');
    $pageTitle = 'Home - ' . SITE_NAME;
    include $rootDir . '/pages/home.php';
}

// Footer
include $rootDir . '/includes/footer.php';
?>