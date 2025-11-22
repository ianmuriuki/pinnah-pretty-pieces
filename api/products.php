<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // For local JS
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log request
$logFile = __DIR__ . '/../debug.log';
error_log('DEBUG: API products called - action: ' . ($_GET['action'] ?? 'list'));

// Safe load
$rootDir = __DIR__ . '/..';
try {
    require_once $rootDir . '/config/config.php';
    require_once $rootDir . '/config/database.php';
    require_once $rootDir . '/includes/functions.php';
} catch (Exception $e) {
    error_log('DEBUG: API load fail: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'API setup error: ' . $e->getMessage()]);
    exit();
}

$db = Database::getInstance();
$action = sanitizeInput($_GET['action'] ?? 'list');
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'list':
        $category = sanitizeInput($_GET['category'] ?? '');
        $search = sanitizeInput($_GET['search'] ?? '');
        $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;

        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($featured !== null) {
            $sql .= " AND is_featured = ?";
            $params[] = (bool)$featured;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 20";  // Limit for performance
        $products = $db->query($sql, $params);
        error_log('DEBUG: Products list returned ' . count($products) . ' items');
        $response = ['success' => true, 'data' => $products];
        break;

    default:
        $response['message'] = 'No action specified';
}

echo json_encode($response);
?>