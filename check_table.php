<?php
require_once 'website/includes/dbConnect.php';
try {
    $stmt = $conn->query("DESCRIBE checkout");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    file_put_contents('result_utf8.json', json_encode($result, JSON_PRETTY_PRINT));
    echo "Done";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>