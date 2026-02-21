<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'webbuilders');

define('BASE_URL', 'https://adminwebbuilders.webbuilders.lk/');

// Start session
if (!session_id()) {
    session_start();
}
