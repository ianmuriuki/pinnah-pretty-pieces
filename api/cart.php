<?php
/**
 * api/cart.php - Synchronized with Frontend
 */

header('Content-Type: application/json');

// Ensure ALLOWED_ORIGINS is defined to prevent 500 errors
require_once __DIR__ . '/../config/config.php';
if (!defined('ALLOWED_ORIGINS')) define('ALLOWED_ORIGINS', ['*']);

header('Access-Control-Allow-Origin: ' . (is_array(ALLOWED_ORIGINS) ? implode(', ', ALLOWED_ORIGINS) : ALLOWED_ORIGINS));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    $db = Database::getInstance();

    switch ($action) {
        case 'add':
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $product_id = (int)($input['product_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 1);

            if ($product_id <= 0) throw new Exception('Invalid product selection.');

            // Check stock
            $product = $db->query("SELECT stock FROM products WHERE id = ?", [$product_id]);
            if (empty($product)) throw new Exception('Product not found.');
            if ($product[0]['stock'] < $quantity) throw new Exception('Insufficient stock available.');

            addToCart($product_id, $quantity);
            $response = ['success' => true, 'message' => 'Added to cart', 'cart_count' => getCartCount()];
            break;

        case 'remove':
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $product_id = (int)($input['product_id'] ?? 0);
            
            if (isLoggedIn()) {
                $db->query("DELETE FROM carts WHERE user_id = ? AND product_id = ?", [getUserId(), $product_id]);
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            $response = ['success' => true, 'message' => 'Removed', 'cart_count' => getCartCount()];
            break;

        case 'update':
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $product_id = (int)($input['product_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 0);

            if (isLoggedIn()) {
                if ($quantity <= 0) {
                    $db->query("DELETE FROM carts WHERE user_id = ? AND product_id = ?", [getUserId(), $product_id]);
                } else {
                    $db->query("UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?", [$quantity, getUserId(), $product_id]);
                }
            } else {
                if ($quantity <= 0) unset($_SESSION['cart'][$product_id]);
                else $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
            $response = ['success' => true, 'message' => 'Updated', 'cart_count' => getCartCount()];
            break;

        case 'list':
            $cartItems = [];
            $total = 0;

            if (isLoggedIn()) {
                $data = $db->query("
                    SELECT p.id, p.name, p.price, p.image, c.quantity 
                    FROM carts c 
                    JOIN products p ON c.product_id = p.id 
                    WHERE c.user_id = ?
                ", [getUserId()]);
            } else {
                $data = [];
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $id => $item) {
                        $p = $db->query("SELECT id, name, price, image FROM products WHERE id = ?", [$id]);
                        if ($p) {
                            $p[0]['quantity'] = $item['quantity'];
                            $data[] = $p[0];
                        }
                    }
                }
            }

            foreach ($data as $item) {
                $subtotal = (float)$item['price'] * (int)$item['quantity'];
                $total += $subtotal;
                // Image path is already complete in DB, just use it directly
                $imagePath = $item['image'] ?: 'assets/images/products/default.jpg';
                $cartItems[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => (float)$item['price'],
                    'image' => $imagePath,
                    'quantity' => (int)$item['quantity'],
                    'subtotal' => $subtotal
                ];
            }

            $response = ['success' => true, 'data' => $cartItems, 'total' => $total, 'cart_count' => getCartCount()];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);