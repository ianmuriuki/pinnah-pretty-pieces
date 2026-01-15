<?php
/**
 * includes/functions.php - Updated for DB Authentication
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php'; 

/**
 * Sanitize user input
 */
function sanitizeInput($input, $type = 'string') {
    if (is_array($input)) return array_map('sanitizeInput', $input);
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    if ($type === 'int') $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    elseif ($type === 'email') $input = filter_var($input, FILTER_SANITIZE_EMAIL);
    return $input;
}

/**
 * Auth Functions
 */
function loginUser($email, $password) {
    $db = Database::getInstance();
    $clean_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // 1. Fetch user by email
    $stmt = $db->pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$clean_email]);
    $user = $stmt->fetch();
    
    // 2. Verify password against the DB hash
    if ($user && password_verify($password, $user['password'])) {
        // Prevent session fixation by regenerating ID (only if no output sent yet)
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        
        // 3. Set Session Variables
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        
        return true;
    }
    return false;
}

/**
 * Get current logged in user details
 */
function getUser() {
    if (!isLoggedIn()) return null;
    $db = Database::getInstance();
    $stmt = $db->pdo->prepare("SELECT id, full_name, email, role, phone FROM users WHERE id = ?");
    $stmt->execute([getUserId()]);
    return $stmt->fetch();
}

/**
 * Logout Helper
 */
function logoutUser() {
    session_unset();
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

/**
 * Cart Functions
 */
function addToCart($productId, $quantity) {
    if (isLoggedIn()) {
        $userId = getUserId();
        $db = Database::getInstance();
        // Uses the custom query method from your database class
        $db->query("
            INSERT INTO carts (user_id, product_id, quantity) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + ?
        ", [$userId, $productId, $quantity, $quantity]);
    } else {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = ['quantity' => $quantity];
        }
    }
}

function getCartCount() {
    if (isLoggedIn()) {
        $db = Database::getInstance();
        $res = $db->query("SELECT SUM(quantity) as total FROM carts WHERE user_id = ?", [getUserId()]);
        return (int)($res[0]['total'] ?? 0);
    }
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) $count += $item['quantity'];
    }
    return $count;
}
?>