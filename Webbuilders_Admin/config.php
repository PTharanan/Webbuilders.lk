<?php 
    define('TWOCHECKOUT_SELLER_ID', '255882059367');
    define('TWOCHECKOUT_PUBLISHABLE_KEY', 'AE9159E1-E9B8-46EA-97CB-4C28D4A7977F');
    define('TWOCHECKOUT_PRIVATE_KEY', '460DBD96-6B26-4F24-B0C6-D4250C65A11C');
    define('CURRENCY_CODE', 'usd');

    define('BASE_URL', 'http://localhost/Webbuilders.lk/Webbuilders_Admin/');    

    

    // Database configuration
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'webbuilders');

    // Start session
    if (!session_id()) {
        session_start();
    }
?>
