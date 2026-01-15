<?php
/**
 * includes/session.php
 */
require_once __DIR__ . '/../config/config.php';

// Force session settings to prevent XAMPP folder permission issues
// MUST be done before session_start()
if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.use_only_cookies', 1);
    @ini_set('session.use_strict_mode', 1);
    @session_start();
}

// Security: Session Timeout Check
$timeout = 3600; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: /pinnahs-pretty-pieces/admin/index.php?expired=1');
    exit();
}
$_SESSION['last_activity'] = time();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Handle Logout via GET
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header('Location: /pinnahs-pretty-pieces/admin/index.php');
    exit();
}