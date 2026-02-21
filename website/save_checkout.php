<?php
header('Content-Type: application/json');
error_reporting(0);
require_once 'includes/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_data = file_get_contents('php://input');
    file_put_contents('checkout_debug.log', "--- " . date('Y-m-d H:i:s') . " ---\nRAW DATA: " . $raw_data . "\n", FILE_APPEND);

    $data = json_decode($raw_data, true);

    if (isset($data['domain']) && isset($data['plan'])) {
        try {
            $payment_id = isset($data['payment_id']) ? $data['payment_id'] : '';
            $order_id = isset($data['order_id']) ? $data['order_id'] : '';

            // Only skip if THIS specific order is already marked as successful with THIS payment ID
            // This prevents page reloads from reprocessing, but allows the update if it's currently 'PENDING'
            if ($payment_id !== 'PENDING' && !empty($payment_id)) {
                $check_sql = "SELECT id FROM checkout WHERE order_id = ? AND payment_id = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->execute([$order_id, $payment_id]);
                if ($check_stmt->fetch()) {
                    echo json_encode(['status' => 'success', 'message' => 'Already processed']);
                    exit;
                }
            }

            $sql = "INSERT INTO checkout (payment_id, order_id, domain, plan, plan_price, domain_price, total_price) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        payment_id = IF(VALUES(payment_id) != 'PENDING', VALUES(payment_id), payment_id),
                        domain = VALUES(domain),
                        plan = VALUES(plan),
                        plan_price = VALUES(plan_price),
                        domain_price = VALUES(domain_price),
                        total_price = VALUES(total_price)";

            $stmt = $conn->prepare($sql);
            $exec_data = [
                isset($data['payment_id']) ? $data['payment_id'] : null,
                isset($data['order_id']) ? $data['order_id'] : null,
                $data['domain'],
                $data['plan'],
                isset($data['planPrice']) ? $data['planPrice'] : 0,
                isset($data['domainPrice']) ? $data['domainPrice'] : 0,
                isset($data['total']) ? $data['total'] : 0
            ];
            file_put_contents('checkout_debug.log', "EXEC DATA: " . print_r($exec_data, true) . "\n", FILE_APPEND);
            $stmt->execute($exec_data);

            $last_id = $conn->lastInsertId();
            file_put_contents('checkout_debug.log', "SUCCESS: Processed " . ($data['order_id'] ?? 'N/A') . "\n", FILE_APPEND);
            echo json_encode(['status' => 'success', 'id' => $last_id]);
        } catch (Exception $e) {
            file_put_contents('checkout_debug.log', "DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        file_put_contents('checkout_debug.log', "ERROR: Invalid data - domain or plan missing\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    file_put_contents('checkout_debug.log', "ERROR: Invalid request method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>