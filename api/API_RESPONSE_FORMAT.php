<?php
/**
 * API Response Format Guide
 * 
 * Tất cả API responses phải tuân theo format này để đảm bảo consistency
 */

// Success Response
$successResponse = [
    'status' => 'success',
    'message' => 'Operation successful',
    'data' => []
];

// Error Response
$errorResponse = [
    'status' => 'error',
    'message' => 'Error description',
    'code' => 'ERROR_CODE'
];

// Paginated Response
$paginatedResponse = [
    'status' => 'success',
    'message' => 'Data retrieved',
    'data' => [
        'products' => [],
        'page' => 1,
        'limit' => 20,
        'total' => 100,
        'totalPages' => 5
    ]
];

// List Response
$listResponse = [
    'status' => 'success',
    'message' => 'List retrieved',
    'data' => []
];

// Single Item Response
$itemResponse = [
    'status' => 'success',
    'message' => 'Item retrieved',
    'data' => []
];

// HTTP Status Codes
$statusCodes = [
    200 => 'OK',
    201 => 'Created',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not Found',
    500 => 'Internal Server Error'
];

/**
 * Response Headers
 * 
 * Tất cả file API phải set headers sau:
 * - Content-Type: application/json
 * - Access-Control-Allow-Origin: *
 * - Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
 * - Access-Control-Allow-Headers: Content-Type
 */

/**
 * Common Response Patterns
 */

// Lấy danh sách (GET)
// HTTP 200
// {
//   "status": "success",
//   "data": [...],
//   "page": 1,
//   "limit": 20,
//   "total": 100,
//   "totalPages": 5
// }

// Lấy chi tiết (GET)
// HTTP 200
// {
//   "status": "success",
//   "data": {...}
// }

// Tạo (POST)
// HTTP 201
// {
//   "status": "success",
//   "message": "Created successfully",
//   "data": {...}
// }

// Cập nhật (PUT)
// HTTP 200
// {
//   "status": "success",
//   "message": "Updated successfully"
// }

// Xóa (DELETE)
// HTTP 200
// {
//   "status": "success",
//   "message": "Deleted successfully"
// }

// Lỗi
// HTTP 400/401/500
// {
//   "status": "error",
//   "message": "Error description"
// }
?>
