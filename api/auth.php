<?php
/*
 * Authentication API for Pinnah's Pretty Pieces
 * Endpoints: POST /api/auth.php?action=register, POST /api/auth.php?action=login, GET /api/auth.php?action=logout
 * Returns JSON. Include in forms via AJAX (fetch() in main.js).
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . implode(', ', ALLOWED_ORIGINS));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $full_name = sanitizeInput($input['full_name'] ?? '');
        $email = sanitizeInput($input['email'] ?? '', 'email');
        $password = $input['password'] ?? '';
        $phone = sanitizeInput($input['phone'] ?? '');

        if (empty($full_name) || empty($email) || empty($password) || strlen($password) < 6) {
            $response['message'] = 'Invalid input';
            break;
        }

        $db = Database::getInstance();
        $check = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
        if (!empty($check)) {
            $response['message'] = 'Email already registered';
            break;
        }

        $hashed = hashPassword($password);
        $stmt = $db->pdo->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$full_name, $email, $hashed, $phone])) {
            sendWelcomeEmail($email, $full_name);
            $response = ['success' => true, 'message' => 'Registered successfully. Please login.'];
        } else {
            $response['message'] = 'Registration failed';
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $email = sanitizeInput($input['email'] ?? '', 'email');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            $response['message'] = 'Invalid credentials';
            break;
        }

        if (loginUser($email, $password)) {
            $response = ['success' => true, 'message' => 'Logged in successfully', 'user' => ['email' => $email]];
        } else {
            $response['message'] = 'Invalid email or password';
        }
        break;

    case 'logout':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $response['message'] = 'Method not allowed';
            break;
        }
        logoutUser();
        $response = ['success' => true, 'message' => 'Logged out successfully'];
        break;

    default:
        $response['message'] = 'No action specified';
}

echo json_encode($response);
?>