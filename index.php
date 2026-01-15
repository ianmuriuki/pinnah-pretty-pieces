<?php
// DEV: Errors/log (Turn ON to see the actual cause of the blank page)
ini_set('display_errors', 1); // Temporarily set to 1 to find the crash
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
$logFile = __DIR__ . '/debug.log';
ini_set('log_errors', 1);
ini_set('error_log', $logFile);
header('Content-Type: text/html; charset=UTF-8');

$rootDir = __DIR__;

// Load core
try {
    require_once $rootDir . '/config/config.php';
    require_once $rootDir . '/config/database.php';
    require_once $rootDir . '/includes/functions.php';
    require_once $rootDir . '/includes/session.php';
} catch (Throwable $e) { // Changed to Throwable to catch fatal errors too
    error_log('DEBUG: Core fail: ' . $e->getMessage());
    http_response_code(500);
    die('<h1 style="color: red; text-align: center;">Setup Error</h1><p>Check debug.log: ' . htmlspecialchars($e->getMessage()) . '</p>');
}

// Global Logout Handler
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    logoutUser();
    header("Location: index.php?page=home");
    exit();
}

// 1. ROUTING & SANITIZATION
$page = sanitizeInput($_GET['page'] ?? 'home');

// 2. EXPLICIT ROUTE MAPPING (Fixes the "signup" button issue)
// This maps the URL keyword 'signup' to the physical file 'register.php'
if ($page === 'signup') {
    $pageFile = $rootDir . '/pages/register.php';
} else {
    $pageFile = $rootDir . '/pages/' . $page . '.php';
}

$pageTitle = ucfirst(str_replace('-', ' ', $page)) . ' - ' . SITE_NAME;

// 3. ACCESS CONTROL (Fixes the Profile blank page if session is broken)
$protected_pages = ['profile', 'checkout'];
if (in_array($page, $protected_pages)) {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login&msg=unauthorized');
        exit();
    }
}

// 4. HEADER
include $rootDir . '/includes/header.php';

// 5. PAGE CONTENT LOADER
if (file_exists($pageFile)) {
    // If a page like profile.php exists but is blank, the error is INSIDE profile.php
    include $pageFile;
} else {
    error_log("DEBUG: Page file missing: " . $pageFile);
    $pageTitle = 'Home - ' . SITE_NAME;
    include $rootDir . '/pages/home.php';
}

// 6. FOOTER
include $rootDir . '/includes/footer.php';
?>