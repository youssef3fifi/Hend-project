<?php
/**
 * Application Configuration
 * General settings for the Book Store application
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting - disable in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Application settings
define('APP_NAME', 'BookStore');
define('ITEMS_PER_PAGE', 12);
define('ADMIN_SESSION_KEY', 'admin_logged_in');

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');

// Generate CSRF token if not exists
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

/**
 * Get CSRF token for forms
 * @return string CSRF token
 */
function getCsrfToken() {
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verifyCsrfToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Sanitize output to prevent XSS
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is admin
 * @return bool True if admin
 */
function isAdmin() {
    return isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] === true;
}

/**
 * Get base URL dynamically
 * @return string Base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base === '/' || $base === '\\') {
        $base = '';
    }
    return $protocol . "://" . $host . $base;
}

// Set base URL constant
define('BASE_URL', getBaseUrl());
?>
