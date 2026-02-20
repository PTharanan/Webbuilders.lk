<?php
require_once 'website/includes/dbConnect.php';
try {
    $conn->exec("ALTER TABLE checkout 
        ADD COLUMN IF NOT EXISTS plan_price DECIMAL(10,2), 
        ADD COLUMN IF NOT EXISTS domain_price DECIMAL(10,2), 
        ADD COLUMN IF NOT EXISTS total_price DECIMAL(10,2), 
        ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    echo "Success";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>