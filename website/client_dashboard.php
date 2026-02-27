<?php
require_once 'includes/dbConnect.php';

// Check if client is logged in
if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
    header("Location: client_dashboard_login.php");
    exit;
}

$clientEmail = $_SESSION['client_email'];

// PayHere API Configuration
define('PAYHERE_APP_ID', '4OVyblaLIC84JH5EsPSJf73PV');
define('PAYHERE_APP_SECRET', '4p9OCkqtTk14TpzG8sKSId4fWWBvcC8RR4kmfw1RqJCD');
define('PAYHERE_SANDBOX_ENDPOINT', 'https://sandbox.payhere.lk/merchant/v1');
define('PAYHERE_LIVE_ENDPOINT', 'https://www.payhere.lk/merchant/v1');
define('PAYHERE_API_ENDPOINT', PAYHERE_SANDBOX_ENDPOINT);

function getAccessToken()
{
    $auth = base64_encode(PAYHERE_APP_ID . ':' . PAYHERE_APP_SECRET);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/oauth/token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $auth, 'Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }
    return null;
}

function getAllSubscriptions($accessToken)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/subscription');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return ($data['status'] == 1) ? $data['data'] : [];
}

function getSubscriptionPayments($subId, $accessToken)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/subscription/' . $subId . '/payments');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['data'] ?? [];
}

$clientSubscriptions = [];
$accessToken = getAccessToken();

if ($accessToken) {
    // 1. Get all local subscription IDs from the orders table
    $localSubIds = [];
    try {
        $stmt = $conn->prepare("SELECT DISTINCT sub_ID FROM orders WHERE sub_ID IS NOT NULL AND sub_ID != ''");
        $stmt->execute();
        $localSubIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // Silent fail or log
    }

    $allSubs = getAllSubscriptions($accessToken);
    foreach ($allSubs as $sub) {
        // Filter by logged in client email AND check if it exists in local orders table
        if (isset($sub['customer']['email']) && strtolower($sub['customer']['email']) === strtolower($clientEmail)) {
            if (in_array($sub['subscription_id'], $localSubIds)) {
                $sub['payments'] = getSubscriptionPayments($sub['subscription_id'], $accessToken);
                $clientSubscriptions[] = $sub;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - WEBbuilders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/client_dashboard_login.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        tc: {
                            DEFAULT: '#f97316',
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#92400e',
                            900: '#78350f'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .enhanced-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        .enhanced-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #f97316, #ea580c);
        }

        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: #f97316;
            border-radius: 2px;
        }

        .logout-btn-wrap {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn-logout {
            background: #ff4d4d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #e60000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 77, 77, 0.3);
        }

        /* Modal Styles */
        .logout-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-icon {
            font-size: 50px;
            color: #ff4d4d;
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            justify-content: center;
        }

        .btn-confirm {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cancel {
            background: #f1f1f1;
            color: #333;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-confirm:hover {
            background: #e60000;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .orange-dashboard-banner {
            position: relative;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <section class="orange-dashboard-banner">
        <div class="logout-btn-wrap">
            <button class="btn-logout" onclick="showLogoutModal()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
        <div class="banner-content">
            <h1>Client Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['client_username'] ?? 'Client'); ?></p>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 py-12">
        <?php if (empty($clientSubscriptions)): ?>
            <div class="enhanced-card p-12 text-center">
                <div class="text-tc text-5xl mb-4">
                    <i class="fas fa-box-open"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">No Active Packages</h2>
                <p class="text-gray-500 mt-2">You don't have any active subscription packages at the moment.</p>
                <a href="home.php#pricing"
                    class="inline-block mt-6 bg-tc text-white px-8 py-3 rounded-full font-bold hover:bg-tc-600 transition-all shadow-lg hover:shadow-tc/30">Explore
                    Packages</a>
            </div>
        <?php else: ?>
            <div class="space-y-12">
                <?php foreach ($clientSubscriptions as $sub):
                    $status = strtoupper($sub['status'] ?? 'PENDING');
                    $dateStr = date('M d, Y', strtotime($sub['date']));
                    $amount = number_format($sub['amount'] ?? 0, 2);
                    $subId = $sub['subscription_id'];
                    $payments = $sub['payments'] ?? [];

                    // Customer Details
                    $firstName = $sub['customer']['fist_name'] ?? $sub['customer']['first_name'] ?? '';
                    $lastName = $sub['customer']['last_name'] ?? '';
                    $phone = $sub['customer']['phone'] ?? '-';
                    $city = $sub['customer']['delivery_details']['city'] ?? '-';
                    ?>
                    <div class="enhanced-card animate-in fade-in slide-in-from-bottom-4 duration-700">
                        <!-- Header -->
                        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h2 class="text-2xl font-extrabold text-slate-900">
                                        <?php echo htmlspecialchars($sub['description'] ?? 'Subscription Package'); ?></h2>
                                    <p class="text-slate-500 font-medium">Subscription ID: <span
                                            class="text-tc">#<?php echo htmlspecialchars($subId); ?></span></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-4 py-1.5 rounded-full text-sm font-bold <?php echo ($status === 'ACTIVE') ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'; ?>">
                                        <i
                                            class="fas <?php echo ($status === 'ACTIVE') ? 'fa-check-circle' : 'fa-clock'; ?> mr-1"></i>
                                        <?php echo $status; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            <!-- Top Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Customer Details
                                    </h3>
                                    <div class="space-y-1">
                                        <p class="text-slate-900 font-bold">
                                            <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></p>
                                        <p class="text-slate-600 text-sm"><?php echo htmlspecialchars($clientEmail); ?></p>
                                        <p class="text-slate-600 text-sm"><?php echo htmlspecialchars($phone); ?></p>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Billing Address
                                    </h3>
                                    <div class="space-y-1">
                                        <p class="text-slate-600 text-sm"><?php echo htmlspecialchars($city); ?></p>
                                        <p class="text-slate-600 text-sm">Sri Lanka</p>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Payment Info
                                    </h3>
                                    <div class="space-y-1">
                                        <p class="text-slate-900 font-bold">Rs. <?php echo $amount; ?></p>
                                        <p class="text-slate-600 text-sm">Period:
                                            <?php echo htmlspecialchars($sub['recurring'] ?? '-'); ?></p>
                                        <p class="text-slate-600 text-sm">Started: <?php echo $dateStr; ?></p>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Active Domain
                                    </h3>
                                    <div class="space-y-1">
                                        <a href="<?php echo htmlspecialchars($sub['description']); ?>" target="_blank"
                                            class="text-blue-600 font-bold hover:underline transition-all block truncate">
                                            <?php echo htmlspecialchars($sub['description']); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Details Section -->
                            <div class="bg-slate-50 rounded-2xl p-6 mb-10 border border-slate-100">
                                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-list-ul text-tc"></i> Item Details
                                </h3>
                                <div class="space-y-3">
                                    <?php if (isset($sub['items']) && is_array($sub['items'])): ?>
                                        <?php foreach ($sub['items'] as $item): ?>
                                            <div class="flex justify-between items-center py-2 border-b border-slate-200 last:border-0">
                                                <p class="text-slate-600"><?php echo htmlspecialchars($item['name']); ?> <span
                                                        class="text-slate-400 text-xs ml-2">(Rs.<?php echo number_format($item['unit_price'], 2); ?>
                                                        x <?php echo $item['quantity']; ?>)</span></p>
                                                <p class="text-slate-900 font-bold">Rs.
                                                    <?php echo number_format($item['total_price'], 2); ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="flex justify-between items-center py-2">
                                            <p class="text-slate-600"><?php echo htmlspecialchars($sub['description']); ?></p>
                                            <p class="text-slate-900 font-bold">Rs. <?php echo $amount; ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div
                                    class="mt-4 pt-4 border-t-2 border-dashed border-slate-200 flex justify-between items-center">
                                    <p class="font-bold text-slate-900">Total Recurring Amount</p>
                                    <p class="text-xl font-black text-tc">Rs. <?php echo $amount; ?></p>
                                </div>
                            </div>

                            <!-- Payments Table -->
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-history text-tc"></i> Payment History
                                </h3>
                                <div class="overflow-hidden rounded-xl border border-slate-200">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                                            <tr>
                                                <th class="px-6 py-4 font-bold">Date</th>
                                                <th class="px-6 py-4 font-bold">Payment ID</th>
                                                <th class="px-6 py-4 font-bold">Status</th>
                                                <th class="px-6 py-4 font-bold text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <?php if (!empty($payments)): ?>
                                                <?php foreach ($payments as $payment): ?>
                                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                                        <td class="px-6 py-4 text-slate-600">
                                                            <?php echo date('Y-m-d H:i', strtotime($payment['date'])); ?></td>
                                                        <td class="px-6 py-4 font-medium text-slate-900">
                                                            <?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                                        <td class="px-6 py-4">
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Success
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 text-right font-bold text-slate-900">Rs.
                                                            <?php echo number_format($payment['amount'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">No payment
                                                        transactions found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Info -->
                        <div
                            class="p-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <p class="text-xs text-slate-400 font-medium italic"><i class="fas fa-info-circle mr-1"></i> Data
                                automatically synchronized with PayHere Automated Charging API</p>
                            <div class="flex gap-4">
                                <button class="text-tc font-bold text-sm hover:underline"
                                    onclick="alert('Please contact support to upgrade your package.')">Upgrade Package</button>
                                <span class="text-slate-300">|</span>
                                <button class="text-slate-400 font-bold text-sm hover:text-red-500 transition-colors"
                                    onclick="alert('For security reasons, please contact technical support to cancel your subscription.')">Cancel
                                    Subscription</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <h3>Ready to Leave?</h3>
            <p>Are you sure you want to log out of your dashboard?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                <button class="btn-confirm" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmLogout() {
            window.location.href = 'client_logout.php';
        }

        // Close modal if clicked outside
        window.onclick = function (event) {
            var modal = document.getElementById('logoutModal');
            if (event.target == modal) {
                closeLogoutModal();
            }
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>