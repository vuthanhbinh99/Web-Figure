<?php
/**
 * API Bootstrap - Centralized initialization for all API endpoints
 * Handles CORS, error reporting, database connection
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set JSON header and CORS immediately (BEFORE any other output)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Expose-Headers: Content-Type');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Suppress warnings in production, but show for debugging
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error: $errstr in $errfile on line $errline");
    return false; // Continue with normal error handling
});

// Load database
try {
    require_once __DIR__ . '/../config/database.php';
    
    if (!isset($conn)) {
        throw new Exception('Database connection not initialized');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
    error_log('DB Connection Error: ' . $e->getMessage());
    exit;
}

// Helper function for error response
function errorResponse($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

// Helper function for success response
function successResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
    exit;
}
?>
