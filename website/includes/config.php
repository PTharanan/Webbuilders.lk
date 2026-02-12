<?php
    // Database configuration
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'webbuilders');

    define('BASE_URL', 'http://localhost/Webbuilders.lk/Webbuilders_Admin/');    

    // Start session
    if (!session_id()) {
        session_start();
    }
?>
