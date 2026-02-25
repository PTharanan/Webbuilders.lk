<?php
require_once 'auth_check.php';
/**
 * PayHere All Subscribers
 * Displays all subscribers from PayHere API
 */

// Include database connection
require_once 'dbConnect.php';

// Set page variables for layout
$pageTitle = 'Subscribers';
$pageSubtitle = 'Manage PayHere subscriptions';

// PayHere API Configuration
define('PAYHERE_APP_ID', '4OVyblaLIC84JH5EsPSJf73PV');
define('PAYHERE_APP_SECRET', '4p9OCkqtTk14TpzG8sKSId4fWWBvcC8RR4kmfw1RqJCD');

// PayHere API Endpoints
define('PAYHERE_SANDBOX_ENDPOINT', 'https://sandbox.payhere.lk/merchant/v1');
define('PAYHERE_LIVE_ENDPOINT', 'https://www.payhere.lk/merchant/v1');

// Use Sandbox for testing (change to LIVE_ENDPOINT for production)
define('PAYHERE_API_ENDPOINT', PAYHERE_SANDBOX_ENDPOINT);

// Get current user ID from session
$userId = $_SESSION['user_id'];

// Handle AJAX requests for subscription details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getSubscriptionDetails') {
    header('Content-Type: application/json');

    $subId = $_GET['sub_id'] ?? null;
    if (!$subId) {
        echo json_encode(['error' => 'Subscription ID not provided']);
        exit;
    }

    $accessToken = getAccessToken();
    if (!$accessToken) {
        echo json_encode(['error' => 'Failed to authenticate with PayHere']);
        exit;
    }

    // Fetch single subscription directly
    $subscriptionData = getSubscriptionById($subId, $accessToken);

    if (!$subscriptionData) {
        echo json_encode(['error' => 'Subscription not found']);
        exit;
    }

    // Fetch subscription payments
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/subscription/' . $subId . '/payments');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $paymentResponse = curl_exec($ch);
    $paymentHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $paymentData = [];
    if ($paymentHttpCode === 200) {
        $paymentResponseData = json_decode($paymentResponse, true);
        $paymentData = $paymentResponseData['data'] ?? [];
    }

    echo json_encode([
        'subscription' => $subscriptionData,
        'payments' => $paymentData
    ]);
    exit;
}

// Function to get a single subscription by ID
function getSubscriptionById($subscriptionId, $accessToken = null)
{
    if (!$accessToken || empty($subscriptionId)) {
        return null;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/subscription/' . $subscriptionId);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] == 1 && isset($data['data'])) {
            return $data['data'];
        }
    }

    return null;
}

// Function to get Access Token
function getAccessToken()
{
    // Create Basic Auth header
    $auth = base64_encode(PAYHERE_APP_ID . ':' . PAYHERE_APP_SECRET);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/oauth/token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic ' . $auth,
        'Content-Type: application/x-www-form-urlencoded'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    error_log("PayHere OAuth Error: HTTP $httpCode - $error");
    return null;
}

// Function to get subscription payment history
function getSubscriptionHistory($subscriptionId, $accessToken = null)
{
    if (!$accessToken || empty($subscriptionId)) {
        return array();
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/subscription/' . $subscriptionId . '/transactions');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }
    }

    return array();
}

// Function to get all subscriptions from PayHere
function getAllSubscriptions($accessToken = null)
{
    if (!$accessToken) {
        return array(
            'success' => false,
            'message' => 'No access token available',
            'subscriptions' => array()
        );
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYHERE_API_ENDPOINT . '/subscription');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);

        if (isset($data['status']) && $data['status'] == 1 && isset($data['data'])) {
            return array(
                'success' => true,
                'message' => $data['msg'] ?? 'Success',
                'subscriptions' => $data['data']
            );
        } else {
            return array(
                'success' => false,
                'message' => $data['msg'] ?? 'API returned unsuccessful status',
                'subscriptions' => array()
            );
        }
    }

    return array(
        'success' => false,
        'message' => 'HTTP ' . $httpCode . ': ' . $error,
        'subscriptions' => array()
    );
}

// Get Access Token
$accessToken = getAccessToken();

// Initialize variables
$allSubscriptions = array();
$subscriptionMessage = '';

// Get all subscriptions if we have access token
if ($accessToken) {
    // 1. First check DB for required subscription IDs
    $dbSubIds = [];
    try {
        $stmt = $conn->prepare("SELECT DISTINCT sub_ID FROM orders WHERE sub_ID IS NOT NULL AND sub_ID != ''");
        $stmt->execute();
        $dbSubIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $subscriptionMessage = "Database error: " . $e->getMessage();
    }

    if (!empty($dbSubIds)) {
        // 2. Fetch the current list from PayHere (one quick call)
        $listResult = getAllSubscriptions($accessToken);
        $payhereList = $listResult['subscriptions'] ?? [];
        $subscriptionMessage = $listResult['message'];

        // Map list by ID for easy lookup
        $idToSub = [];
        foreach ($payhereList as $sub) {
            $idToSub[$sub['subscription_id']] = $sub;
        }

        // 3. Match and fetch missing ones individually
        foreach ($dbSubIds as $subId) {
            if (isset($idToSub[$subId])) {
                $allSubscriptions[] = $idToSub[$subId];
            } else {
                // If not in the recent list, try fetching it directly
                $directSub = getSubscriptionById($subId, $accessToken);
                if ($directSub) {
                    $allSubscriptions[] = $directSub;
                }
            }
        }
    } else {
        $subscriptionMessage = 'No subscriptions found in the local orders table.';
    }
} else {
    $subscriptionMessage = 'Failed to get access token. Check your App ID and App Secret.';
}

// Build page content using output buffering
ob_start();
?>


<!-- Error/Success Messages -->
<?php if (!$accessToken): ?>
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Authentication Failed</h3>
                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                    <?php echo htmlspecialchars($subscriptionMessage); ?><br>
                    <strong>Troubleshooting:</strong><br>
                    1. Verify App ID and App Secret are correct<br>
                    2. Ensure API Key has "Automated Charging API" permission<br>
                    3. Check that you're using the correct environment (Sandbox/Live)
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Main Content Card -->
<div class="enhanced-card">
    <?php if (!$accessToken): ?>
        <!-- No Access Token -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4v2m0 4v2m0-14V5m0 4V7m0 4V9m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Connection Error</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Unable to connect to PayHere API</p>
        </div>
    <?php elseif (empty($allSubscriptions)): ?>
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No Subscriptions Found</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">There are no subscriptions in your PayHere account yet
            </p>
        </div>
    <?php else: ?>
        <!-- Success Message -->
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Successfully Loaded</h3>
                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">Found
                        <strong><?php echo count($allSubscriptions); ?></strong> subscription(s)
                    </p>
                </div>
            </div>
        </div>

        <!-- Subscribers Table with Payment History -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">SUB ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">PAYMENT NO</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">DATE</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">CUSTOMER</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">EMAIL</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">PLAN</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">AMOUNT</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">FREQUENCY</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">STATUS</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($allSubscriptions as $subscription):
                        $status = strtolower($subscription['status'] ?? 'unknown');
                        if ($status === 'active') {
                            $badgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-700';
                        } elseif ($status === 'inactive' || $status === 'failed') {
                            $badgeClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-300 dark:border-red-700';
                        } elseif ($status === 'pending') {
                            $badgeClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700';
                        } else {
                            $badgeClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600';
                        }

                        $customer = $subscription['customer'] ?? array();
                        $firstName = $customer['fist_name'] ?? $customer['first_name'] ?? '';
                        $lastName = $customer['last_name'] ?? '';
                        $subId = $subscription['subscription_id'] ?? '';
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                        <?php echo htmlspecialchars(substr($subId, 0, 12)); ?>
                                                    </code>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                <code
                                    class="text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-2 py-1 rounded">
                                                    <?php echo htmlspecialchars($subId); ?>
                                                </code>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                <?php echo htmlspecialchars($subscription['date'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                <?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                <?php echo htmlspecialchars($subscription['description'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                LKR <?php echo number_format($subscription['amount'] ?? 0, 2); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                <?php echo htmlspecialchars($subscription['recurring'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars(ucfirst($status)); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    onclick="openHistoryModal('<?php echo htmlspecialchars($subId); ?>', '<?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>', '<?php echo htmlspecialchars($subscription['date'] ?? 'now'); ?>', <?php echo $subscription['amount'] ?? 0; ?>)"
                                    class="inline-block px-4 py-2 bg-tc text-white rounded-lg hover:bg-tc-600 transition-colors text-xs font-semibold">
                                    History
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Stats -->
        <div class="mt-8 flex flex-wrap gap-4 text-sm">
            <?php
            $activeCount = 0;
            $inactiveCount = 0;
            $pendingCount = 0;
            foreach ($allSubscriptions as $sub) {
                $status = strtolower($sub['status'] ?? 'unknown');
                if ($status === 'active')
                    $activeCount++;
                elseif ($status === 'inactive' || $status === 'failed')
                    $inactiveCount++;
                elseif ($status === 'pending')
                    $pendingCount++;
            }
            ?>
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-gray-600 dark:text-gray-400"><strong
                        class="text-gray-900 dark:text-white"><?php echo $activeCount; ?></strong> Active</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-gray-600 dark:text-gray-400"><strong
                        class="text-gray-900 dark:text-white"><?php echo $pendingCount; ?></strong> Pending</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-gray-600 dark:text-gray-400"><strong
                        class="text-gray-900 dark:text-white"><?php echo $inactiveCount; ?></strong> Inactive</span>
            </div>
        </div>

        <script>
            function openHistoryModal(subId, customerName, startDate, amount) {
                // Show loading state
                let modalHTML = `
                    <div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                            <div class="sticky top-0 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Subscription Details</h2>
                                <button onclick="closeHistoryModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-6 text-center">
                                <div class="inline-block">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mt-4">Loading subscription details...</p>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                let existingModal = document.getElementById('historyModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Insert modal
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                // Fetch subscription details from server
                fetch('?action=getSubscriptionDetails&sub_id=' + subId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            showModalError(data.error);
                            return;
                        }

                        const sub = data.subscription;
                        const payments = data.payments || [];

                        // Extract customer information
                        const firstName = sub.customer?.fist_name || '';
                        const lastName = sub.customer?.last_name || '';
                        const custName = firstName + ' ' + lastName;
                        const custEmail = sub.customer?.email || '-';
                        const custPhone = sub.customer?.phone || '-';

                        // Extract billing information
                        const billingCity = sub.customer?.delivery_details?.city || '-';
                        const billingCountry = sub.customer?.delivery_details?.country || 'Sri Lanka';

                        // Format date
                        const dateStr = new Date(sub.date).toLocaleString('en-US', {
                            year: 'numeric', month: 'short', day: 'numeric',
                            hour: '2-digit', minute: '2-digit'
                        });

                        // Get status
                        const status = sub.status || 'PENDING';
                        const statusText = status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();

                        // Build items HTML
                        let itemsHTML = '';
                        let totalAmount = 0;
                        if (sub.items && Array.isArray(sub.items)) {
                            sub.items.forEach(item => {
                                itemsHTML += `
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                        <p class="text-gray-600 dark:text-gray-400">${item.name} (Rs.${parseFloat(item.unit_price).toFixed(2)} x ${item.quantity})</p>
                                        <p class="text-gray-900 dark:text-white">Rs.${parseFloat(item.total_price).toFixed(2)}</p>
                                    </div>
                                `;
                                totalAmount = item.total_price;
                            });
                        }

                        // Build payments table
                        let paymentsHTML = '<tr class="border-b border-gray-200 dark:border-gray-700"><td colspan="4" class="text-center py-3 text-gray-600 dark:text-gray-400">No authorized payments yet</td></tr>';
                        if (payments.length > 0) {
                            paymentsHTML = payments.map(payment => {
                                const paymentDate = new Date(payment.date).toLocaleString('en-US', {
                                    year: 'numeric', month: '2-digit', day: '2-digit',
                                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                                });
                                return `
                                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${paymentDate}</td>
                                        <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-400">${payment.payment_id || '-'}</td>
                                        <td class="px-4 py-3 text-sm"><span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded text-xs font-semibold">${payment.status || 'Authorized'}</span></td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-right">Rs. ${parseFloat(payment.amount).toFixed(2)}</td>
                                    </tr>
                                `;
                            }).join('');
                        }

                        // Create detailed modal content
                        let detailedModalHTML = `
                            <div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                                    <!-- Header -->
                                    <div class="sticky top-0 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Subscription Details</h2>
                                        <button onclick="closeHistoryModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="p-6">
                                        <!-- Status -->
                                        <div class="mb-6">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status :</p>
                                            <p class="text-lg font-semibold">
                                                <span class="px-4 py-1 rounded ${status === 'ACTIVE' ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'}">
                                                    ${statusText}
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <!-- Customer & Billing Details -->
                                        <div class="grid grid-cols-2 gap-6 mb-6">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Customer Details</h3>
                                                <div class="space-y-1 text-sm">
                                                    <p class="text-gray-900 dark:text-white font-medium">${custName}</p>
                                                    <p class="text-gray-600 dark:text-gray-400">${custEmail}</p>
                                                    <p class="text-gray-600 dark:text-gray-400">${custPhone}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Billing Details</h3>
                                                <div class="space-y-1 text-sm">
                                                    <p class="text-gray-600 dark:text-gray-400">${billingCity}</p>
                                                    <p class="text-gray-600 dark:text-gray-400"></p>
                                                    <p class="text-gray-600 dark:text-gray-400">${billingCountry}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Order & Payment Details -->
                                        <div class="grid grid-cols-2 gap-6 mb-6">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Order Details</h3>
                                                <div class="space-y-2 text-sm">
                                                    <p class="text-gray-600 dark:text-gray-400"><strong>Subscription ID:</strong> #${subId}</p>
                                                    <p class="text-gray-600 dark:text-gray-400"><strong>Domain:</strong> <a href="${sub.description}" class="text-blue-600 dark:text-blue-400 hover:underline truncate">${sub.description || '-'}</a></p>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Payment Details</h3>
                                                <div class="space-y-2 text-sm">
                                                    <p class="text-gray-600 dark:text-gray-400"><strong>Date :</strong> ${dateStr}</p>
                                                    <p class="text-gray-600 dark:text-gray-400"><strong>Amount :</strong> ${parseFloat(sub.amount).toFixed(2)}</p>
                                                    <p class="text-gray-600 dark:text-gray-400"><strong>Period :</strong> ${sub.recurring || '-'}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Item Details -->
                                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 mb-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Item Details</h3>
                                            <div class="space-y-0">
                                                ${itemsHTML}
                                            </div>
                                            <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3 flex justify-between items-center font-semibold text-gray-900 dark:text-white">
                                                <p>Total</p>
                                                <p>Rs.${parseFloat(totalAmount).toFixed(2)}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Cancel Button -->
                                        <div class="mb-6">
                                            <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-lg transition">Cancel Subscription</button>
                                        </div>
                                        
                                        <!-- Payments Table -->
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Received Payments via this Subscription</h3>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-sm">
                                                    <thead class="bg-gray-200 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left text-gray-900 dark:text-white font-semibold">Date</th>
                                                            <th class="px-4 py-3 text-left text-gray-900 dark:text-white font-semibold">Payment No</th>
                                                            <th class="px-4 py-3 text-left text-gray-900 dark:text-white font-semibold">Status</th>
                                                            <th class="px-4 py-3 text-right text-gray-900 dark:text-white font-semibold">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${paymentsHTML}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Replace modal
                        let existingModal = document.getElementById('historyModal');
                        if (existingModal) {
                            existingModal.remove();
                        }
                        document.body.insertAdjacentHTML('beforeend', detailedModalHTML);
                    })
                    .catch(error => {
                        console.error('Error fetching subscription details:', error);
                        showModalError('Failed to load subscription details. Please try again.');
                    });
            }

            function showModalError(message) {
                let errorModalHTML = `
                    <div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-md w-full">
                            <div class="p-6">
                                <div class="text-red-600 dark:text-red-400 text-lg mb-2">Error</div>
                                <p class="text-gray-700 dark:text-gray-300 mb-6">${message}</p>
                                <button onclick="closeHistoryModal()" class="w-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-2 rounded transition">Close</button>
                            </div>
                        </div>
                    </div>
                `;
                let existingModal = document.getElementById('historyModal');
                if (existingModal) {
                    existingModal.remove();
                }
                document.body.insertAdjacentHTML('beforeend', errorModalHTML);
            }

            function closeHistoryModal() {
                let modal = document.getElementById('historyModal');
                if (modal) {
                    modal.remove();
                }
            }

            // Close modal when clicking outside
            document.addEventListener('click', function (event) {
                let modal = document.getElementById('historyModal');
                if (modal && event.target === modal) {
                    closeHistoryModal();
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>