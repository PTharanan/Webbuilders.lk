<?php
require_once 'auth_check.php';
/**
 * PayHere All Subscribers
 * Displays all subscribers from PayHere API
 */

// Include database connection
require_once 'dbConnect.php';

// Set page variables for layout
$pageTitle = 'Clients';
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

// Handle POST request to save client login details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveClientLogin') {
    header('Content-Type: application/json');
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($username) || empty($password) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    try {
        // Check if email already exists
        $check = $conn->prepare("SELECT COUNT(*) FROM client WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Client login already exists for this email']);
            exit;
        }

        // Insert new client with hashed password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO client (username, password, email) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $hashedPassword, $email]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Login details saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save login details']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Handle GET request to get single client login details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getClientLogin') {
    header('Content-Type: application/json');
    $email = $_GET['email'] ?? '';
    try {
        $stmt = $conn->prepare("SELECT username FROM client WHERE email = ?");
        $stmt->execute([$email]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($client) {
            echo json_encode(['success' => true, 'data' => $client]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Client not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

// Handle POST request to update client login details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateClientLogin') {
    header('Content-Type: application/json');
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE client SET username = ?, password = ? WHERE email = ?");
        $result = $stmt->execute([$username, $hashedPassword, $email]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

// Handle POST request to delete client login details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteClient') {
    header('Content-Type: application/json');
    $email = $_POST['email'] ?? '';
    try {
        $stmt = $conn->prepare("DELETE FROM client WHERE email = ?");
        $result = $stmt->execute([$email]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
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
        $tempAllSubscriptions = [];
        foreach ($dbSubIds as $subId) {
            if (isset($idToSub[$subId])) {
                $tempAllSubscriptions[] = $idToSub[$subId];
            } else {
                // If not in the recent list, try fetching it directly
                $directSub = getSubscriptionById($subId, $accessToken);
                if ($directSub) {
                    $tempAllSubscriptions[] = $directSub;
                }
            }
        }

        // 4. Group by Email
        $groupedClients = [];
        foreach ($tempAllSubscriptions as $sub) {
            $email = $sub['customer']['email'] ?? 'N/A';
            if (!isset($groupedClients[$email])) {
                $groupedClients[$email] = [
                    'name' => ($sub['customer']['fist_name'] ?? $sub['customer']['first_name'] ?? '') . ' ' . ($sub['customer']['last_name'] ?? ''),
                    'email' => $email,
                    'subscriptions' => []
                ];
            }
            $groupedClients[$email]['subscriptions'][] = $sub;
        }
        $allSubscriptions = array_values($groupedClients);
    } else {
        $subscriptionMessage = 'No subscriptions found in the local orders table.';
    }
} else {
    $subscriptionMessage = 'Failed to get access token. Check your App ID and App Secret.';
}


// Fetch existing client emails from local database
$existingClientEmails = [];
try {
    $stmt = $conn->query("SELECT email FROM client");
    $existingClientEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Silent fail
}

// Build page content using output buffering
ob_start();
?>

<!-- Premium Notification Hub -->
<div id="notification-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3 min-w-[320px]"></div>

<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-notification {
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
</style>


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
                        <strong><?php echo count($allSubscriptions); ?></strong> unique client(s)
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <div class="md:col-span-12">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="clientSearch" onkeyup="filterClients()" placeholder="Search by customer email..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-[#F36611] focus:border-[#F36611] transition-colors outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Clients Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">CUSTOMER</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">EMAIL</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Login deatils</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- No Results Row -->
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-search text-3xl opacity-20"></i>
                                <span>No matching clients found for your search.</span>
                            </div>
                        </td>
                    </tr>
                    <?php foreach ($allSubscriptions as $client): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer"
                            data-client="<?php echo htmlspecialchars(json_encode($client), ENT_QUOTES, 'UTF-8'); ?>"
                            onclick="handleClientRowClick(this)">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                <?php echo htmlspecialchars($client['name']); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <?php echo htmlspecialchars($client['email']); ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if (in_array($client['email'], $existingClientEmails)): ?>
                                    <div class="flex items-center gap-3">
                                        <button onclick="event.stopPropagation(); editClient('<?php echo $client['email']; ?>')"
                                            class="p-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all border border-blue-200 dark:border-blue-800"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button
                                            onclick="event.stopPropagation(); confirmDeleteModal('<?php echo $client['email']; ?>')"
                                            class="p-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-all border border-red-200 dark:border-red-800"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <button
                                        onclick="event.stopPropagation(); addClientLogin('<?php echo $client['email']; ?>', '<?php echo htmlspecialchars($client['name'], ENT_QUOTES); ?>')"
                                        class="bg-[#F36611] hover:bg-[#d9560a] text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-sm flex items-center gap-2 hover:-translate-y-0.5">
                                        <i class="fas fa-user-plus"></i> Add login deatils
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>


                </tbody>
            </table>
        </div>




        <script>
            function filterClients() {
                const input = document.getElementById('clientSearch');
                const filter = input.value.toLowerCase();
                const table = document.querySelector('table');
                const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => row.id !== 'noResultsRow');
                const noResultsRow = document.getElementById('noResultsRow');
                let found = false;

                rows.forEach(row => {
                    const emailCell = row.cells[1];
                    if (emailCell) {
                        const emailText = emailCell.textContent || emailCell.innerText;
                        if (emailText.toLowerCase().indexOf(filter) > -1) {
                            row.style.display = "";
                            found = true;
                        } else {
                            row.style.display = "none";
                        }
                    }
                });

                if (noResultsRow) {
                    noResultsRow.classList.toggle('hidden', found);
                }
            }

            function handleClientRowClick(row) {
                const client = JSON.parse(row.dataset.client);
                openClientDetails(client);
            }

            function addClientLogin(email, name) {
                const modalHTML = `
                    <div id="loginModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4 animate-in fade-in duration-200">
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 dark:border-gray-800 animate-in zoom-in duration-200">
                            <div class="bg-gradient-to-r from-tc to-tc-600 p-6 relative">
                                <button type="button" onclick="closeLoginModal()" 
                                    class="absolute right-4 top-4 text-white/70 hover:text-white transition-colors text-lg">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-user-shield"></i> Add Login Details
                                </h3>
                                <p class="text-white/80 text-xs mt-1">Creating account for ${name}</p>
                            </div>
                            
                            <form id="loginForm" class="p-6 space-y-4">
                                <input type="hidden" name="email" value="${email}">
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 px-1">Username</label>
                                    <input type="text" name="username" id="loginUsername" required autofocus
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-tc focus:border-transparent outline-none transition-all dark:text-white"
                                        placeholder="Enter username">
                                </div>
                                
                                <div class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 px-1">Password</label>
                                    <div class="relative group">
                                        <input type="password" name="password" id="loginPassword" required
                                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-tc focus:border-transparent outline-none transition-all dark:text-white"
                                            placeholder="••••••••">
                                        <button type="button" onclick="togglePassword('loginPassword', this)" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-tc transition-colors">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 px-1">Confirm Password</label>
                                    <div class="relative group">
                                        <input type="password" id="loginConfirmPassword" required
                                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-tc focus:border-transparent outline-none transition-all dark:text-white"
                                            placeholder="••••••••">
                                        <button type="button" onclick="togglePassword('loginConfirmPassword', this)" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-tc transition-colors">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex gap-3 pt-4">
                                    <button type="button" onclick="clearLoginForm()" 
                                        class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-eraser"></i> Clear
                                    </button>
                                    <button type="button" onclick="submitLoginForm()" id="doneBtn"
                                        class="flex-1 px-6 py-3 bg-tc text-white font-bold rounded-xl hover:bg-tc-600 shadow-lg shadow-tc/20 transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i> Done
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHTML);
                document.getElementById('loginUsername').focus();
            }

            // Premium Notification System
            function showNotification(message, type = 'success') {
                const container = document.getElementById('notification-container');
                const notification = document.createElement('div');

                const bgClass = type === 'success' ? 'bg-white dark:bg-gray-800' : 'bg-red-50 dark:bg-red-900/30';
                const borderClass = type === 'success' ? 'border-green-500' : 'border-red-500';
                const iconClass = type === 'success' ? 'text-green-500' : 'text-red-500';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

                notification.className = `animate-notification flex items-center p-4 rounded-2xl shadow-2xl border-l-4 ${bgClass} ${borderClass} transition-all duration-300`;
                notification.innerHTML = `
                    <div class="flex-shrink-0 ${iconClass} text-xl mr-3">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="flex-1 mr-4">
                        <p class="text-xs font-bold uppercase tracking-widest ${type === 'success' ? 'text-gray-400' : 'text-red-400'} mb-0.5">${type}</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 leading-tight">${message}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                container.appendChild(notification);

                // Auto remove after 5 seconds
                const timer = setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(20px)';
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            }

            function togglePassword(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text'; // Show Password
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye'); // Show eye
                } else {
                    input.type = 'password'; // Hide Password
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash'); // Hide eye
                }
            }

            function clearLoginForm() {
                const form = document.getElementById('loginForm');
                if (form) {
                    form.reset();
                    document.getElementById('loginUsername').focus();
                }
            }

            function closeLoginModal() {
                const modal = document.getElementById('loginModal');
                if (modal) {
                    modal.classList.add('animate-out', 'fade-out', 'zoom-out', 'duration-150');
                    setTimeout(() => modal.remove(), 150);
                }
            }

            function submitLoginForm() {
                const form = document.getElementById('loginForm');
                const btn = document.getElementById('doneBtn');
                const username = form.username.value;
                const password = form.password.value;
                const confirm = document.getElementById('loginConfirmPassword').value;
                const email = form.email.value;

                if (!username || !password) {
                    showNotification('Please fill in all fields', 'error');
                    return;
                }

                if (password !== confirm) {
                    showNotification('Passwords do not match', 'error');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${form.isUpdate ? 'Updating...' : 'Saving...'}`;

                const formData = new FormData();
                formData.append('action', form.isUpdate ? 'updateClientLogin' : 'saveClientLogin');
                formData.append('username', username);
                formData.append('password', password);
                formData.append('email', email);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message || 'Saved successfully!');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification(data.message || 'Failed to save', 'error');
                            btn.disabled = false;
                            btn.innerHTML = `<i class="fas fa-check-circle"></i> ${form.isUpdate ? 'Save' : 'Done'}`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred. Please try again.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Done';
                    });
            }

            function editClient(email) {
                fetch(`?action=getClientLogin&email=${email}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const client = data.data;
                            const modalHTML = `
                                <div id="loginModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4 animate-in fade-in duration-200">
                                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 dark:border-gray-800 animate-in zoom-in duration-200">
                                         <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 relative">
                                             <button type="button" onclick="closeLoginModal()" 
                                                 class="absolute right-4 top-4 text-white/70 hover:text-white transition-colors text-lg">
                                                 <i class="fas fa-times"></i>
                                             </button>
                                             <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                                 <i class="fas fa-user-edit"></i> Edit Login Details
                                             </h3>
                                             <p class="text-white/80 text-xs mt-1">Updating account for ${email}</p>
                                         </div>
                                         
                                         <form id="loginForm" class="p-6 space-y-4">
                                             <input type="hidden" name="email" value="${email}">
                                             <input type="hidden" name="isUpdate" value="true">
                                             
                                             <div>
                                                 <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 px-1">Username</label>
                                                 <input type="text" name="username" id="loginUsername" readonly required value="${client.username}"
                                                     class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all dark:text-white"
                                                     placeholder="Enter username">
                                             </div>
                                             
                                             <div class="relative">
                                                 <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 px-1">Password</label>
                                                 <div class="relative group">
                                                     <input type="password" name="password" id="loginPassword" required value=""
                                                         class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all dark:text-white"
                                                         placeholder="Leave empty to keep current">
                                                     <button type="button" onclick="togglePassword('loginPassword', this)" 
                                                         class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 transition-colors">
                                                         <i class="fas fa-eye-slash"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                             
                                             <div class="relative">
                                                 <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 px-1">Confirm Password</label>
                                                 <div class="relative group">
                                                     <input type="password" id="loginConfirmPassword" required value=""
                                                         class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all dark:text-white"
                                                         placeholder="Confirm new password">
                                                     <button type="button" onclick="togglePassword('loginConfirmPassword', this)" 
                                                         class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 transition-colors">
                                                         <i class="fas fa-eye-slash"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                             
                                             <div class="flex gap-3 pt-4">
                                                 <button type="button" onclick="clearLoginForm()" 
                                                     class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all flex items-center justify-center gap-2">
                                                     <i class="fas fa-eraser"></i> Clear
                                                 </button>
                                                 <button type="button" onclick="submitLoginForm()" id="doneBtn"
                                                     class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                                                     <i class="fas fa-save"></i> Save
                                                 </button>
                                             </div>
                                         </form>
                                    </div>
                                </div>
                            `;
                            document.body.insertAdjacentHTML('beforeend', modalHTML);
                            document.getElementById('loginUsername').focus();
                        } else {
                            showNotification(data.message, 'error');
                        }
                    });
            }

            function deleteClient(email) {
                const formData = new FormData();
                formData.append('action', 'deleteClient');
                formData.append('email', email);

                const btn = document.getElementById('confirmDeleteBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';
                }

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Deleted successfully');
                            closeDeleteModal();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification(data.message, 'error');
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = 'Delete Permanently';
                            }
                        }
                    });
            }

            function confirmDeleteModal(email) {
                const modalHTML = `
                    <div id="deleteConfirmModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4 animate-in fade-in duration-200">
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-gray-100 dark:border-gray-800 animate-in zoom-in duration-200">
                            <div class="p-8 text-center">
                                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600 dark:text-red-400">
                                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Are you sure?</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8">
                                    You are about to delete the login details for <span class="font-bold text-gray-900 dark:text-white">${email}</span>. This action cannot be undone.
                                </p>
                                <div class="flex flex-col gap-3">
                                    <button id="confirmDeleteBtn" onclick="deleteClient('${email}')" 
                                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-600/20">
                                        Delete Permanently
                                    </button>
                                    <button onclick="closeDeleteModal()" 
                                        class="w-full py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHTML);
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteConfirmModal');
                if (modal) {
                    modal.classList.add('animate-out', 'fade-out', 'zoom-out', 'duration-150');
                    setTimeout(() => modal.remove(), 150);
                }
            }

            function openClientDetails(client) {
                const subscriptions = client.subscriptions || [];

                let rowsHTML = '';
                subscriptions.forEach(sub => {
                    const status = (sub.status || 'unknown').toLowerCase();
                    let badgeClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600';
                    if (status === 'active') {
                        badgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-700';
                    } else if (status === 'inactive' || status === 'failed') {
                        badgeClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-300 dark:border-red-700';
                    } else if (status === 'pending') {
                        badgeClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700';
                    }

                    const subId = sub.subscription_id || '';
                    const date = sub.date || 'N/A';
                    const plan = sub.description || 'N/A';
                    const amount = parseFloat(sub.amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const freq = sub.recurring || 'N/A';
                    const customerName = client.name;

                    rowsHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium border-b border-gray-100 dark:border-gray-800">
                                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                    ${String(subId).substring(0, 12)}
                                </code>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium border-b border-gray-100 dark:border-gray-800">
                                <code class="text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-2 py-1 rounded">
                                    ${subId}
                                </code>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs border-b border-gray-100 dark:border-gray-800">
                                ${date}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-800">
                                ${plan}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium border-b border-gray-100 dark:border-gray-800">
                                LKR ${amount}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs border-b border-gray-100 dark:border-gray-800">
                                ${freq}
                            </td>
                            <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                                    ${status.charAt(0).toUpperCase() + status.slice(1)}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                                <button onclick="event.stopPropagation(); openHistoryModal('${subId}', '${customerName}', '${date}', ${sub.amount || 0})"
                                    class="inline-block px-4 py-2 bg-tc text-white rounded-lg hover:bg-tc-600 transition-colors text-xs font-semibold shadow-sm">
                                    History
                                </button>
                            </td>
                        </tr>
                    `;
                });

                let modalHTML = `
                    <div id="clientDetailsModal" class="fixed inset-0 bg-black bg-opacity-60 z-40 flex items-center justify-center p-4 backdrop-blur-sm">
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
                            <!-- Premium Form-like Header -->
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customer Subscriptions</h2>
                                        <div class="grid grid-cols-2 gap-x-8 gap-y-2 mt-4">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">${client.name}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">${client.email}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Plans</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">${subscriptions.length}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="closeClientModal()" class="p-2 hover:bg-white dark:hover:bg-gray-700 rounded-full transition-all shadow-sm group">
                                        <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Content Area -->
                            <div class="p-0 overflow-y-auto flex-1">
                                <table class="w-full text-sm">
                                    <thead class="sticky top-0 bg-white dark:bg-gray-900 z-10">
                                        <tr>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">SUB ID</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">PAYMENT NO</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">DATE</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">PLAN</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">AMOUNT</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">FREQUENCY</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">STATUS</th>
                                            <th class="px-4 py-4 text-left font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        ${rowsHTML}
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Footer -->
                            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-end">
                                <button onclick="closeClientModal()" class="px-8 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-all font-bold text-sm shadow-sm">
                                    Close Window
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                document.body.insertAdjacentHTML('beforeend', modalHTML);
            }

            function closeClientModal() {
                const modal = document.getElementById('clientDetailsModal');
                if (modal) {
                    modal.classList.add('animate-out', 'fade-out', 'zoom-out', 'duration-150');
                    setTimeout(() => modal.remove(), 150);
                }
            }

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
                        const firstName = sub.customer?.fist_name || sub.customer?.first_name || '';
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
                                        
                                        <div class="mb-6">
                                            <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-lg transition" onclick="showNotification('Feature coming soon', 'error')">Cancel Subscription</button>
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

            // Close modals when clicking outside
            document.addEventListener('click', function (event) {
                let historyModal = document.getElementById('historyModal');
                if (historyModal && event.target === historyModal) {
                    closeHistoryModal();
                }

                let clientModal = document.getElementById('clientDetailsModal');
                if (clientModal && event.target === clientModal) {
                    closeClientModal();
                }

                let deleteModal = document.getElementById('deleteConfirmModal');
                if (deleteModal && event.target === deleteModal) {
                    closeDeleteModal();
                }

                let loginModal = document.getElementById('loginModal');
                if (loginModal && event.target === loginModal) {
                    closeLoginModal();
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>