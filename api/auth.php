<?php
/**
 * Authentication API Endpoint
 * Handles admin login/logout
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        handlePost();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

/**
 * Handle POST requests - Login or logout
 */
function handlePost() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        if ($data['action'] === 'login') {
            handleLogin($data);
        } elseif ($data['action'] === 'logout') {
            handleLogout();
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing action']);
    }
}

/**
 * Handle login
 */
function handleLogin($data) {
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing credentials']);
        return;
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, password_hash, is_admin FROM users WHERE username = ? AND is_admin = TRUE");
    $stmt->bind_param("s", $data['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($data['password'], $row['password_hash'])) {
            $_SESSION[ADMIN_SESSION_KEY] = true;
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
    
    $stmt->close();
}

/**
 * Handle logout
 */
function handleLogout() {
    unset($_SESSION[ADMIN_SESSION_KEY]);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    echo json_encode(['success' => true, 'message' => 'Logout successful']);
}
?>
