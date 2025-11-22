<?php
/*
 * Orders API for Pinnah's Pretty Pieces
 * Endpoints: POST /api/orders.php (checkout/create), GET /api/orders.php?action=list (user orders)
 * Processes cart to order, updates stock, sends email. Admin can view all.
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

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit();
}

$userId = getUserId();
$action = $_GET['action'] ?? 'create';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $shipping_address = sanitizeInput($input['shipping_address'] ?? '');
        $payment_method = sanitizeInput($input['payment_method'] ?? 'card');

        if (empty($shipping_address)) {
            $response['message'] = 'Shipping address required';
            break;
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Get cart items
            $cartData = $db->query("
                SELECT c.product_id, c.quantity, p.price 
                FROM carts c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
            ", [$userId]);

            if (empty($cartData)) {
                $response['message'] = 'Cart empty';
                break;
            }

            $total = 0;
            foreach ($cartData as $item) {
                $total += $item['price'] * $item['quantity'];
                // Update stock
                $db->query("UPDATE products SET stock = stock - ? WHERE id = ?", [$item['quantity'], $item['product_id']]);
            }

            // Create order
            $stmt = $db->pdo->prepare("
                INSERT INTO orders (user_id, total, status, shipping_address, payment_method) 
                VALUES (?, ?, 'pending', ?, ?)
            ");
            $stmt->execute([$userId, $total, $shipping_address, $payment_method]);
            $orderId = $db->lastInsertId();

            // Add order items
            $itemStmt = $db->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartData as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }

            // Clear cart
            $db->query("DELETE FROM carts WHERE user_id = ?", [$userId]);
            unset($_SESSION['cart']);

            $db->commit();
            sendOrderConfirmation(getUser()['email'], $orderId, $total);
            $response = ['success' => true, 'message' => 'Order created successfully', 'order_id' => $orderId, 'total' => $total];
        } catch (Exception $e) {
            $db->rollback();
            $response['message'] = 'Order failed: ' . $e->getMessage();
        }
        break;

    case 'list':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $db = Database::getInstance();
        $orders = $db->query("
            SELECT o.*, COUNT(oi.id) as item_count 
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.user_id = ? 
            GROUP BY o.id 
            ORDER BY o.created_at DESC
        ", [$userId]);

        $response = ['success' => true, 'data' => $orders];
        break;

    default:
        $response['message'] = 'No action specified';
}

echo json_encode($response);
?>