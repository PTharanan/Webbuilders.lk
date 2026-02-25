<?php
/**
 * notify.php - PayHere Payment Notification Handler
 * Integrated with dbConnect.php
 */

// 1. Include the database connection file
require_once 'includes/dbConnect.php';

// Logging (for debugging and verifying PayHere POST data)
file_put_contents('payhere_live_log.txt', "\n--- Notification " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
file_put_contents('payhere_live_log.txt', print_r($_POST, true), FILE_APPEND);

// 2. Retrieve PayHere notification data
$merchant_id = $_POST['merchant_id'] ?? '';
$order_id = $_POST['order_id'] ?? '';
$payhere_amount = $_POST['payhere_amount'] ?? '0.00';
$payhere_currency = $_POST['payhere_currency'] ?? 'LKR';
$status_code = $_POST['status_code'] ?? '';
$md5sig = $_POST['md5sig'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';

// Robust Subscription ID capture:
// PayHere sends the data as 'subscription_id' in the POST request.
// We then save this value into your table's 'sub_ID' column.
$payhere_subscription_id = $_POST['subscription_id'] ?? ($_POST['item_code'] ?? 'N/A');

// 3. Security Verification (Local MD5 Signature)
// Merchant Secret is used to verify that the notification came from PayHere
$merchant_secret = 'MjE5NzM5NDYxMTE3NzEwODk0NjM4Njg2MzcyNzAyMTM2NDU3MTgx';

$local_md5sig = strtoupper(
    md5(
        $merchant_id .
        $order_id .
        $payhere_amount .
        $payhere_currency .
        $status_code .
        strtoupper(md5($merchant_secret))
    )
);

// 4. If signature matches and payment status is successful (Status 2)
if ($local_md5sig === $md5sig && $status_code == 2) {

    // Retrieve card holder name, default to 'Guest'
    $full_name = $_POST['card_holder_name'] ?? 'Guest';

    try {
        // Prepare SQL to insert order details into the 'orders' table
        // We use 'sub_ID' column for the actual PayHere Subscription ID
        $sql = "INSERT INTO orders (
                    order_id, payment_id, sub_ID, full_name, 
                    amount, status, created_at
                ) VALUES (
                    :order_id, :payment_id, :sub_ID, :full_name, 
                    :amount, :status, NOW()
                )";

        $stmt = $conn->prepare($sql);

        // Generate a fallback Order ID for our system if not provided by PayHere
        $final_order_id = !empty($order_id) ? $order_id : ('SUB-' . time());

        $stmt->execute([
            ':order_id' => $final_order_id,
            ':payment_id' => $payment_id,
            ':sub_ID' => $payhere_subscription_id,
            ':full_name' => $full_name,
            ':amount' => $payhere_amount,
            ':status' => $status_code
        ]);

        file_put_contents('payhere_live_log.txt', "RESULT: SUCCESS - Saved to DB (Sub: $payhere_subscription_id)\n", FILE_APPEND);

        // --- ALSO UPDATE CHECKOUT TABLE ---
        try {
            $updateCheckoutSql = "UPDATE checkout SET payment_id = :payment_id WHERE order_id = :order_id";
            $updateStmt = $conn->prepare($updateCheckoutSql);
            $updateStmt->execute([
                ':payment_id' => $payment_id,
                ':order_id' => $final_order_id
            ]);
            file_put_contents('payhere_live_log.txt', "RESULT: CHECKOUT TABLE UPDATED\n", FILE_APPEND);
        } catch (PDOException $e) {
            file_put_contents('payhere_live_log.txt', "CHECKOUT UPDATE ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }
        // ---------------------------------

    } catch (PDOException $e) {
        file_put_contents('payhere_live_log.txt', "DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    }
} else {
    // Log if signature mismatch occurs or payment failed
    file_put_contents('payhere_live_log.txt', "RESULT: SIG MISMATCH OR FAILED STATUS\n", FILE_APPEND);
}
?>