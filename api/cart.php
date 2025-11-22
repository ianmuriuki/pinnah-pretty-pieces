<?php
/*
 * Cart API for Pinnah's Pretty Pieces
 * Endpoints: POST /api/cart.php?action=add, POST /api/cart.php?action=remove, POST /api/cart.php?action=update, GET /api/cart.php?action=list
 * Supports guests (session) and users (DB). Returns JSON with items/prices.
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
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $product_id = (int)($input['product_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? 1);

        if ($product_id <= 0 || $quantity <= 0) {
            $response['message'] = 'Invalid product or quantity';
            break;
        }

        // Check stock
        $db = Database::getInstance();
        $product = $db->query("SELECT stock FROM products WHERE id = ?", [$product_id]);
        if (empty($product) || $product[0]['stock'] < $quantity) {
            $response['message'] = 'Insufficient stock';
            break;
        }

        addToCart($product_id, $quantity);
        $response = ['success' => true, 'message' => 'Added to cart', 'cart_count' => getCartCount()];
        break;

    case 'remove':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $product_id = (int)($input['product_id'] ?? 0);

        if ($product_id <= 0) {
            $response['message'] = 'Invalid product';
            break;
        }

        if (isLoggedIn()) {
            $userId = getUserId();
            $db = Database::getInstance();
            $db->query("DELETE FROM carts WHERE user_id = ? AND product_id = ?", [$userId, $product_id]);
        } else {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        }
        $response = ['success' => true, 'message' => 'Removed from cart', 'cart_count' => getCartCount()];
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $product_id = (int)($input['product_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? 0);

        if ($product_id <= 0 || $quantity < 0) {
            $response['message'] = 'Invalid input';
            break;
        }

        if (isLoggedIn()) {
            $userId = getUserId();
            $db = Database::getInstance();
            if ($quantity === 0) {
                $db->query("DELETE FROM carts WHERE user_id = ? AND product_id = ?", [$userId, $product_id]);
            } else {
                $db->query("UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?", [$quantity, $userId, $product_id]);
            }
        } else {
            if ($quantity === 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
            $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        }
        $response = ['success' => true, 'message' => 'Cart updated', 'cart_count' => getCartCount()];
        break;

    case 'list':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $response['message'] = 'Method not allowed';
            break;
        }
        $cartItems = [];
        $total = 0;

        if (isLoggedIn()) {
            $userId = getUserId();
            $db = Database::getInstance();
            $cartData = $db->query("
                SELECT c.product_id, c.quantity, p.name, p.price, p.image 
                FROM carts c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
            ", [$userId]);

            foreach ($cartData as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                $cartItems[] = [
                    'id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];
            }
        } else {
            if (isset($_SESSION['cart'])) {
                $db = Database::getInstance();
                foreach ($_SESSION['cart'] as $id => $item) {
                    $product = $db->query("SELECT name, price, image FROM products WHERE id = ?", [(int)$id]);
                    if (!empty($product)) {
                        $subtotal = $product[0]['price'] * $item['quantity'];
                        $total += $subtotal;
                        $cartItems[] = [
                            'id' => (int)$id,
                            'name' => $product[0]['name'],
                            'price' => $product[0]['price'],
                            'image' => $product[0]['image'],
                            'quantity' => $item['quantity'],
                            'subtotal' => $subtotal
                        ];
                    }
                }
            }
        }

        $response = [
            'success' => true,
            'data' => $cartItems,
            'total' => $total,
            'count' => count($cartItems)
        ];
        break;

    default:
        $response['message'] = 'No action specified';
}

echo json_encode($response);
?>