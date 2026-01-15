<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

// Simple CORS (Adjust if needed)
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

// Get JSON or Form Data
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

switch ($action) {
    case 'register':
        $full_name = sanitizeInput($input['full_name'] ?? '');
        $email = sanitizeInput($input['email'] ?? '', 'email');
        $password = $input['password'] ?? '';
        $phone = sanitizeInput($input['phone'] ?? '');

        // 1. Validation check
        if (empty($full_name) || empty($email) || strlen($password) < 6) {
            $response['message'] = 'Valid Name, Email, and 6-char Password required';
            break;
        }

        try {
            $db = Database::getInstance();
            
            // 2. Check for duplicate email using the unique constraint in schema
            $checkStmt = $db->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                $response['message'] = 'Email already registered';
                break;
            }

            // 3. Hash password using the method recommended in your schema
            // Note: Your schema notes suggest password_hash() is required
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // 4. Insert into database
            $stmt = $db->pdo->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, 'user')");
            if ($stmt->execute([$full_name, $email, $hashed, $phone])) {
                $response = ['success' => true, 'message' => 'Registered successfully! Please login.'];
            } else {
                $response['message'] = 'Registration failed due to a database error.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Server error: ' . $e->getMessage();
        }
        break;

    case 'login':
        $email = sanitizeInput($input['email'] ?? '', 'email');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            $response['message'] = 'Email and password required';
            break;
        }

        // Uses the loginUser function from your includes/functions.php
        if (loginUser($email, $password)) {
            $response = ['success' => true, 'message' => 'Logged in successfully'];
        } else {
            $response['message'] = 'Invalid email or password';
        }
        break;

    case 'update_profile':
        if (!isLoggedIn()) {
            $response['message'] = 'Login required';
            break;
        }

        $full_name = sanitizeInput($input['full_name'] ?? '');
        $phone = sanitizeInput($input['phone'] ?? '');
        $password = $input['password'] ?? '';
        $user_id = $_SESSION['user_id'];

        if (empty($full_name)) {
            $response['message'] = 'Full name is required';
            break;
        }

        $db = Database::getInstance();
        
        try {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $response['message'] = 'New password must be at least 6 characters';
                    break;
                }
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->pdo->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE id = ?");
                $params = [$full_name, $phone, $hashed, $user_id];
            } else {
                $stmt = $db->pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
                $params = [$full_name, $phone, $user_id];
            }

            if ($stmt->execute($params)) {
                $_SESSION['user_name'] = $full_name; // Refresh name in UI header
                $response = ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                $response['message'] = 'Database error updating profile';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Update failed: ' . $e->getMessage();
        }
        break;

    case 'logout':
        logoutUser(); // Handled in session.php/functions.php
        $response = ['success' => true, 'message' => 'Logged out'];
        break;

    default:
        $response['message'] = 'No valid action provided';
}

echo json_encode($response);