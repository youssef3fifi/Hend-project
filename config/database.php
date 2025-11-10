<?php
/**
 * Database Configuration
 * Environment-aware database connection for AWS EC2 deployment
 */

// Database configuration using environment variables with fallback defaults
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'bookstore_user');
define('DB_PASS', getenv('DB_PASS') ?: 'bookstore_password');
define('DB_NAME', getenv('DB_NAME') ?: 'bookstore_db');

/**
 * Get database connection
 * @return mysqli Database connection object
 * @throws Exception if connection fails
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            throw new Exception("Database connection failed");
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

/**
 * Close database connection
 */
function closeDBConnection() {
    static $conn = null;
    if ($conn !== null) {
        $conn->close();
        $conn = null;
    }
}
?>
