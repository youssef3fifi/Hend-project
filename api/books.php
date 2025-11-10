<?php
/**
 * Books API Endpoint
 * Handles CRUD operations for books
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

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'GET':
            handleGet($conn);
            break;
        case 'POST':
            handlePost($conn);
            break;
        case 'PUT':
            handlePut($conn);
            break;
        case 'DELETE':
            handleDelete($conn);
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
 * Handle GET requests - List books or get single book
 */
function handleGet($conn) {
    if (isset($_GET['id'])) {
        // Get single book
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id WHERE b.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found']);
        }
        $stmt->close();
    } else {
        // List books with filters and pagination
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $where = [];
        $params = [];
        $types = "";
        
        // Filter by category
        if (isset($_GET['category']) && $_GET['category'] !== '') {
            $where[] = "b.category_id = ?";
            $params[] = intval($_GET['category']);
            $types .= "i";
        }
        
        // Filter by search term
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $types .= "sss";
        }
        
        // Filter by price range
        if (isset($_GET['min_price'])) {
            $where[] = "b.price >= ?";
            $params[] = floatval($_GET['min_price']);
            $types .= "d";
        }
        
        if (isset($_GET['max_price'])) {
            $where[] = "b.price <= ?";
            $params[] = floatval($_GET['max_price']);
            $types .= "d";
        }
        
        $whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM books b " . $whereClause;
        if (count($params) > 0) {
            $countStmt = $conn->prepare($countSql);
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $total = $countResult->fetch_assoc()['total'];
            $countStmt->close();
        } else {
            $countResult = $conn->query($countSql);
            $total = $countResult->fetch_assoc()['total'];
        }
        
        // Get books
        $sql = "SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id " . $whereClause . " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt = $conn->prepare($sql);
        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $books,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => intval($total),
                'pages' => ceil($total / $limit)
            ]
        ]);
        
        $stmt->close();
    }
}

/**
 * Handle POST requests - Create new book
 */
function handlePost($conn) {
    // Check admin permission
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['title']) || !isset($data['author']) || !isset($data['price'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO books (title, author, description, price, category_id, isbn, stock_quantity, image_url, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $title = $data['title'];
    $author = $data['author'];
    $description = $data['description'] ?? null;
    $price = floatval($data['price']);
    $category_id = isset($data['category_id']) ? intval($data['category_id']) : null;
    $isbn = $data['isbn'] ?? null;
    $stock_quantity = isset($data['stock_quantity']) ? intval($data['stock_quantity']) : 0;
    $image_url = $data['image_url'] ?? null;
    $rating = isset($data['rating']) ? floatval($data['rating']) : 0.0;
    
    $stmt->bind_param("ssssisssd", $title, $author, $description, $price, $category_id, $isbn, $stock_quantity, $image_url, $rating);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id, 'message' => 'Book created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create book']);
    }
    
    $stmt->close();
}

/**
 * Handle PUT requests - Update book
 */
function handlePut($conn) {
    // Check admin permission
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing book ID']);
        return;
    }
    
    $id = intval($data['id']);
    
    $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, description = ?, price = ?, category_id = ?, isbn = ?, stock_quantity = ?, image_url = ?, rating = ? WHERE id = ?");
    
    $title = $data['title'];
    $author = $data['author'];
    $description = $data['description'] ?? null;
    $price = floatval($data['price']);
    $category_id = isset($data['category_id']) ? intval($data['category_id']) : null;
    $isbn = $data['isbn'] ?? null;
    $stock_quantity = isset($data['stock_quantity']) ? intval($data['stock_quantity']) : 0;
    $image_url = $data['image_url'] ?? null;
    $rating = isset($data['rating']) ? floatval($data['rating']) : 0.0;
    
    $stmt->bind_param("ssssisssdi", $title, $author, $description, $price, $category_id, $isbn, $stock_quantity, $image_url, $rating, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update book']);
    }
    
    $stmt->close();
}

/**
 * Handle DELETE requests - Delete book
 */
function handleDelete($conn) {
    // Check admin permission
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }
    
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing book ID']);
        return;
    }
    
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete book']);
    }
    
    $stmt->close();
}
?>
