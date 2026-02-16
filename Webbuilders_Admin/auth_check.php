<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // For AJAX requests, return JSON error
        header('Content-Type: application/json');
        http_response_code(401);
        die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
    } else {
        // For regular requests, redirect to login
        header('Location: index.php');
        exit;
    }
}

// Role-based access control
$userRole = $_SESSION['user_role'] ?? 'user';
$currentPage = basename($_SERVER['PHP_SELF']);

// Whitelist for staff role
$staffAllowedPages = [
    'attendance.php',
    'adminDocs.php',
    '404.php',
    'logout.php',
    'attendance_handler.php', // Allow handlers for background tasks
    'employee_handler.php',
    'attendance_export.php'
];

if ($userRole === 'staff') {
    if (!in_array($currentPage, $staffAllowedPages)) {
        // If it's an AJAX request, return unauthorized
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(403);
            die(json_encode(['success' => false, 'message' => 'Permission denied']));
        }
        // Otherwise redirect to 404.php as requested
        header('Location: 404.php');
        exit;
    }
}
?>