<?php
/**
 * Custom Requests API - Final Refactor
 */

// 1. Prevent PHP from showing HTML errors in the middle of our JSON
ini_set('display_errors', 0); 
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// CORS handling
$allowed = defined('ALLOWED_ORIGINS') ? ALLOWED_ORIGINS : ['*'];
header('Access-Control-Allow-Origin: ' . (is_array($allowed) ? implode(', ', $allowed) : $allowed));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$action = $_GET['action'] ?? 'submit';
$response = ['success' => false, 'message' => 'Internal server error'];

try {
    $db = Database::getInstance();

    switch ($action) {
        case 'submit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            // Read JSON or Post data
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true) ?: $_POST;

            // Mapping form fields to DB columns
            $user_id = getUserId(); // Safe helper from functions.php
            $user_name = sanitizeInput($input['full_name'] ?? $input['user_name'] ?? '');
            $user_email = sanitizeInput($input['email'] ?? $input['user_email'] ?? '', 'email');
            $phone = sanitizeInput($input['phone'] ?? '');
            $jewelry_type = sanitizeInput($input['jewelry_type'] ?? '');
            $description = sanitizeInput($input['vision'] ?? $input['description'] ?? '');
            $budget = filter_var($input['budget'] ?? 0, FILTER_VALIDATE_FLOAT);
            $occasion = sanitizeInput($input['occasion'] ?? '');

            // Strict Validation
            if (empty($user_name) || empty($user_email) || empty($jewelry_type) || empty($description)) {
                throw new Exception('Missing required fields. Please provide Name, Email, Type, and Vision.');
            }

            // Database Insertion
            $stmt = $db->pdo->prepare("
                INSERT INTO custom_requests (user_id, user_name, user_email, phone, jewelry_type, description, budget, occasion, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())
            ");

            if ($stmt->execute([$user_id, $user_name, $user_email, $phone, $jewelry_type, $description, $budget, $occasion])) {
                
                // Use a try-catch for the email so a mail failure doesn't stop the success message
                try {
                    $requestData = compact('user_name', 'user_email', 'phone', 'jewelry_type', 'description', 'budget', 'occasion');
                    if (function_exists('sendCustomRequestNotification')) {
                        sendCustomRequestNotification($requestData);
                    }
                } catch (Exception $e) {
                    // Log error internally, don't tell the user
                    error_log("Mail Error: " . $e->getMessage());
                }

                $response = [
                    'success' => true, 
                    'message' => 'Request submitted! We will contact you within 24 hours.'
                ];
            } else {
                throw new Exception('Could not save your request. Please try again later.');
            }
            break;

        case 'list':
            if (!isAdmin()) throw new Exception('Unauthorized access');
            $stmt = $db->pdo->query("SELECT * FROM custom_requests ORDER BY created_at DESC LIMIT 100");
            $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    // Return errors as clean JSON
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Clean any accidental whitespace before outputting JSON
ob_clean();
echo json_encode($response);
exit();