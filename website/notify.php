<?php
/**
 * notify.php - Integrated with dbConnect.php
 */

// 1. உங்கள் ஏற்கனவே உள்ள இணைப்புக் கோப்பை அழைக்கவும்
require_once 'includes/dbConnect.php';

// லாக் செய்ய (பிழைத்திருத்தத்திற்காக மட்டும்)
file_put_contents('payhere_live_log.txt', "\n--- Notification " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
file_put_contents('payhere_live_log.txt', print_r($_POST, true), FILE_APPEND);

// 2. PayHere தரவுகளைப் பெறுதல்
$merchant_id = $_POST['merchant_id'] ?? '';
$order_id = $_POST['order_id'] ?? '';
$payhere_amount = $_POST['payhere_amount'] ?? '0.00';
$payhere_currency = $_POST['payhere_currency'] ?? 'LKR';
$status_code = $_POST['status_code'] ?? '';
$md5sig = $_POST['md5sig'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';

// 3. பாதுகாப்பு சரிபார்ப்பு (Security Check)
// உங்கள் படத்திலிருந்து எடுக்கப்பட்ட Secret:
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

// 4. சிக்னேச்சர் மேட்ச் ஆகி, பேமெண்ட் வெற்றியானால் (Status 2)
if ($local_md5sig === $md5sig && $status_code == 2) {

    // தரவுகள் வராத பட்சத்தில் "N/A" எனச் சேமிக்கும்
    $full_name = $_POST['card_holder_name'] ?? 'Guest';
    $email = $_POST['email'] ?? 'N/A';
    $phone = $_POST['phone'] ?? 'N/A';
    $address = $_POST['address'] ?? 'N/A';
    $city = $_POST['city'] ?? 'N/A';

    try {
        // உங்கள் dbConnect.php-இல் உள்ள $conn வேரியபிளைப் பயன்படுத்துகிறோம்
        $sql = "INSERT INTO orders (
                    order_id, payment_id, full_name, email, phone, 
                    address, city, amount, status, created_at
                ) VALUES (
                    :order_id, :payment_id, :full_name, :email, :phone, 
                    :address, :city, :amount, :status, NOW()
                )";

        $stmt = $conn->prepare($sql);

        // Order ID காலியாக இருந்தால் தானாக ஒன்றை உருவாக்குதல்
        $final_id = !empty($order_id) ? $order_id : ('SUB-' . time());

        $stmt->execute([
            ':order_id' => $final_id,
            ':payment_id' => $payment_id,
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':city' => $city,
            ':amount' => $payhere_amount,
            ':status' => $status_code
        ]);

        file_put_contents('payhere_live_log.txt', "RESULT: SUCCESS - Saved to DB\n", FILE_APPEND);

        // --- ALSO UPDATE CHECKOUT TABLE ---
        try {
            $updateCheckoutSql = "UPDATE checkout SET payment_id = :payment_id WHERE order_id = :order_id";
            $updateStmt = $conn->prepare($updateCheckoutSql);
            $updateStmt->execute([
                ':payment_id' => $payment_id,
                ':order_id' => $final_id
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
    // சிக்னேச்சர் தவறாக இருந்தால் லாக் செய்யும்
    file_put_contents('payhere_live_log.txt', "RESULT: SIG MISMATCH OR FAILED STATUS\n", FILE_APPEND);
}
?>