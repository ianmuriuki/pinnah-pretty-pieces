<?php
/**
 * api/orders.php - Finalized Order Processing
 */

header('Content-Type: application/json');

// Ensure Config constants are available
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// CORS
$allowed = defined('ALLOWED_ORIGINS') ? ALLOWED_ORIGINS : ['*'];
header('Access-Control-Allow-Origin: ' . (is_array($allowed) ? implode(', ', $allowed) : $allowed));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// Security Check
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Your session has expired. Please login to continue.']);
    exit();
}

$userId = getUserId();
$action = $_GET['action'] ?? 'create';
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    $db = Database::getInstance();

    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Method not allowed');

            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            // Collect and Sanitize input from our updated Checkout form
            $full_name = sanitizeInput($input['full_name'] ?? '');
            $phone = sanitizeInput($input['phone'] ?? '');
            $shipping_address = sanitizeInput($input['shipping_address'] ?? '');
            $payment_method = sanitizeInput($input['payment_method'] ?? 'mpesa');

            if (empty($shipping_address) || empty($phone)) {
                throw new Exception('Please provide a delivery address and phone number.');
            }

            // Start Transaction to ensure data integrity
            $db->pdo->beginTransaction();

            // 1. Get Cart Items (Joining with products to get current price and check stock)
            $cartData = $db->query("
                SELECT c.product_id, c.quantity, p.price, p.stock, p.name 
                FROM carts c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
            ", [$userId]);

            if (empty($cartData)) throw new Exception('Your cart is empty.');

            $total = 0;
            foreach ($cartData as $item) {
                // Check stock levels before proceeding
                if ($item['stock'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for: " . $item['name']);
                }
                $total += $item['price'] * $item['quantity'];
            }

            // 2. Create the main Order record
            $stmt = $db->pdo->prepare("
                INSERT INTO orders (user_id, total_amount, status, shipping_address, phone, payment_method, created_at) 
                VALUES (?, ?, 'pending', ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $total, $shipping_address, $phone, $payment_method]);
            $orderId = $db->pdo->lastInsertId();

            // 3. Add Order Items and Update Stock
            $itemStmt = $db->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stockStmt = $db->pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($cartData as $item) {
                // Insert into order_items
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
                // Subtract from products stock
                $stockStmt->execute([$item['quantity'], $item['product_id']]);
            }

            // 4. Clear the user's cart
            $db->query("DELETE FROM carts WHERE user_id = ?", [$userId]);
            if (isset($_SESSION['cart'])) unset($_SESSION['cart']);

            // Commit all changes
            $db->pdo->commit();

            // 5. Send Notification (Optional - wrapped in try/catch to prevent crash if mail server fails)
            try {
                if (function_exists('sendOrderConfirmation')) {
                    sendOrderConfirmation($_SESSION['user_email'], $orderId, $total);
                }
            } catch (Exception $e) {
                error_log("Order Email Error: " . $e->getMessage());
            }

            $response = [
                'success' => true, 
                'message' => 'Order placed successfully!', 
                'order_id' => $orderId,
                'total' => $total
            ];
            break;

        case 'list':
            // Fetch user's order history
            $orders = $db->query("
                SELECT id, total_amount as total, status, created_at, shipping_address 
                FROM orders 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ", [$userId]);

            $response = ['success' => true, 'data' => $orders];
            break;

        default:
            throw new Exception('Action not recognized.');
    }
} catch (Exception $e) {
    if (isset($db) && $db->pdo->inTransaction()) {
        $db->pdo->rollBack();
    }
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);