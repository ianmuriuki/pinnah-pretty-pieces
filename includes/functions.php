<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';  // Base DB

// Sanitize
function sanitizeInput($input, $type = 'string') {
    if (is_array($input)) return array_map('sanitizeInput', $input);
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    if ($type === 'int') $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    elseif ($type === 'email') $input = filter_var($input, FILTER_SANITIZE_EMAIL);
    return $input;
}

// Send email (basic mail(), template support)
function sendEmail($to, $subject, $message, $isHtml = true, $template = null) {
    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    if ($isHtml) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    if ($template) {
        $templatePath = __DIR__ . '/../emails/' . $template . '.html';
        if (file_exists($templatePath)) {
            $message = file_get_contents($templatePath);
            $message = str_replace(['{site_name}', '{user_name}', '{message}'], [SITE_NAME, $user_name ?? 'Customer', $message], $message);
        }
    }
    return mail($to, $subject, $message, $headers);
}

// Send welcome
function sendWelcomeEmail($email, $name) {
    $subject = "Welcome to " . SITE_NAME;
    $body = "Hi $name,<br>Thanks for joining!";
    return sendEmail($email, $subject, $body, true, 'welcome');
}

// Send order
function sendOrderConfirmation($email, $orderId, $total) {
    $subject = "Order #" . $orderId . " Confirmed";
    $body = "Total: $" . $total;
    return sendEmail($email, $subject, $body, true, 'order-confirmation');
}

// Send custom
function sendCustomRequestNotification($requestData) {
    $subject = "New Custom Request";
    $body = "Name: " . $requestData['user_name'] . "<br>Description: " . $requestData['description'];
    return sendEmail(ADMIN_EMAIL, $subject, $body, true, 'custom-request');
}

// Cart count (basic session)
function getCartCount() {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    return count($_SESSION['cart']);
}

// Add to cart (session only for test)
function addToCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][$productId] = ['quantity' => $quantity];
    return true;
}

// Password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Upload
function handleUpload($file, $subDir = 'products') {
    $targetDir = UPLOAD_DIR . $subDir . '/';
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, ALLOWED_EXTENSIONS) || $file['size'] > MAX_UPLOAD_SIZE) return false;
    $newName = uniqid() . '.' . $fileExt;
    $targetPath = $targetDir . $newName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return UPLOAD_URL . $subDir . '/' . $newName;
    }
    return false;
}
?>