<?php
/*
 * Custom Requests API for Pinnah's Pretty Pieces
 * Endpoints: POST /api/custom-requests.php (submit form), GET /api/custom-requests.php?action=list (admin view)
 * Stores in DB, sends notification email. WhatsApp via JS in form.
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

$action = $_GET['action'] ?? 'submit';
$response = ['success' => false, 'message' => 'Invalid action'];

if ($action === 'list' && !isAdmin()) {
    $response['message'] = 'Admin access required';
    echo json_encode($response);
    exit();
}

switch ($action) {
    case 'submit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $user_name = sanitizeInput($input['full_name'] ?? $input['user_name'] ?? '');
        $user_email = sanitizeInput($input['email'] ?? $input['user_email'] ?? '', 'email');
        $phone = sanitizeInput($input['phone'] ?? '');
        $jewelry_type = sanitizeInput($input['jewelry_type'] ?? '');
        $description = sanitizeInput($input['vision'] ?? $input['description'] ?? '');
        $budget = (float)($input['budget'] ?? 0);
        $occasion = sanitizeInput($input['occasion'] ?? '');

        if (empty($user_name) || empty($user_email) || empty($jewelry_type) || empty($description)) {
            $response['message'] = 'Missing required fields';
            break;
        }

        $db = Database::getInstance();
        $stmt = $db->pdo->prepare("
            INSERT INTO custom_requests (user_name, user_email, phone, jewelry_type, description, budget, occasion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([$user_name, $user_email, $phone, $jewelry_type, $description, $budget, $occasion])) {
            $requestData = [
                'user_name' => $user_name,
                'user_email' => $user_email,
                'phone' => $phone,
                'jewelry_type' => $jewelry_type,
                'description' => $description,
                'budget' => $budget,
                'occasion' => $occasion
            ];
            sendCustomRequestNotification($requestData);
            $response = ['success' => true, 'message' => 'Request submitted! We\'ll contact you within 24 hours.'];
        } else {
            $response['message'] = 'Submission failed';
        }
        break;

    case 'list':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $db = Database::getInstance();
        $requests = $db->query("
            SELECT * FROM custom_requests 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $response = ['success' => true, 'data' => $requests];
        break;

    default:
        $response['message'] = 'No action specified';
}

echo json_encode($response);
?>