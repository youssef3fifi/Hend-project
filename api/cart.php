<?php
/**
 * Cart API Endpoint
 * Handles shopping cart operations (session-based)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/config.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$session_id = session_id();

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'GET':
            handleGet($conn, $session_id);
            break;
        case 'POST':
            handlePost($conn, $session_id);
            break;
        case 'PUT':
            handlePut($conn, $session_id);
            break;
        case 'DELETE':
            handleDelete($conn, $session_id);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
}

/**
 * Handle GET requests - Get cart items
 */
function handleGet($conn, $session_id) {
    $stmt = $conn->prepare("
        SELECT ci.*, b.title, b.author, b.price, b.image_url, b.stock_quantity,
               (ci.quantity * b.price) as subtotal
        FROM cart_items ci
        JOIN books b ON ci.book_id = b.id
        WHERE ci.session_id = ?
        ORDER BY ci.added_at DESC
    ");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total += floatval($row['subtotal']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $items,
        'total' => $total,
        'count' => count($items)
    ]);
    
    $stmt->close();
}

/**
 * Handle POST requests - Add item to cart
 */
function handlePost($conn, $session_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['book_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing book_id']);
        return;
    }
    
    $book_id = intval($data['book_id']);
    $quantity = isset($data['quantity']) ? max(1, intval($data['quantity'])) : 1;
    
    // Check if book exists and has stock
    $checkStmt = $conn->prepare("SELECT stock_quantity FROM books WHERE id = ?");
    $checkStmt->bind_param("i", $book_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['stock_quantity'] < $quantity) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient stock']);
            $checkStmt->close();
            return;
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
        $checkStmt->close();
        return;
    }
    $checkStmt->close();
    
    // Check if item already in cart
    $existsStmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE session_id = ? AND book_id = ?");
    $existsStmt->bind_param("si", $session_id, $book_id);
    $existsStmt->execute();
    $existsResult = $existsStmt->get_result();
    
    if ($existsRow = $existsResult->fetch_assoc()) {
        // Update quantity
        $newQuantity = $existsRow['quantity'] + $quantity;
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newQuantity, $existsRow['id']);
        $updateStmt->execute();
        $updateStmt->close();
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        // Insert new item
        $insertStmt = $conn->prepare("INSERT INTO cart_items (session_id, book_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sii", $session_id, $book_id, $quantity);
        $insertStmt->execute();
        $insertStmt->close();
        echo json_encode(['success' => true, 'message' => 'Item added to cart']);
    }
    
    $existsStmt->close();
}

/**
 * Handle PUT requests - Update cart item quantity
 */
function handlePut($conn, $session_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['book_id']) || !isset($data['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    $book_id = intval($data['book_id']);
    $quantity = max(0, intval($data['quantity']));
    
    if ($quantity === 0) {
        // Remove item
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE session_id = ? AND book_id = ?");
        $stmt->bind_param("si", $session_id, $book_id);
    } else {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE session_id = ? AND book_id = ?");
        $stmt->bind_param("isi", $quantity, $session_id, $book_id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update cart']);
    }
    
    $stmt->close();
}

/**
 * Handle DELETE requests - Remove item from cart or clear cart
 */
function handleDelete($conn, $session_id) {
    if (isset($_GET['book_id'])) {
        // Remove specific item
        $book_id = intval($_GET['book_id']);
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE session_id = ? AND book_id = ?");
        $stmt->bind_param("si", $session_id, $book_id);
    } elseif (isset($_GET['clear']) && $_GET['clear'] === 'true') {
        // Clear entire cart
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE session_id = ?");
        $stmt->bind_param("s", $session_id);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item(s) removed from cart']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to remove from cart']);
    }
    
    $stmt->close();
}
?>
