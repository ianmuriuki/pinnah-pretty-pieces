<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';  // DB base

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header('Location: ' . SITE_URL . 'index.php?page=login&expired=1');
        exit();
    }
    $_SESSION['last_activity'] = time();
}

// Login
function loginUser($email, $password) {
    $db = Database::getInstance();
    $stmt = $db->pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([sanitizeInput($email, 'email')]);
    $user = $stmt->fetch();
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];
        return true;
    }
    return false;
}

// Logout
function logoutUser() {
    session_unset();
    session_destroy();
    header('Location: ' . SITE_URL . 'index.php');
    exit();
}

// Checks
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUser() {
    if (!isLoggedIn()) return null;
    $db = Database::getInstance();
    $stmt = $db->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([getUserId()]);
    return $stmt->fetch();
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

// Logout handler
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    logoutUser();
}

if (isLoggedIn() && !isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}
?>