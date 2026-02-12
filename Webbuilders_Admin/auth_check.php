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
?>