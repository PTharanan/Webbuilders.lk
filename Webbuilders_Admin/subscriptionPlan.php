<?php
require_once 'auth_check.php';
/**
 * Subscription Plan Settings
 * Manage pricing plans and URLs for domain and hosting packages
 */

// Include database connection FIRST
require_once 'dbConnect.php';

// Verify connection exists (dbConnect.php creates $conn variable)
if (!isset($conn) || $conn === null) {
    die('Database connection failed');
}

// Use $conn as the database connection
$pdo = $conn;

// Set page variables for layout
$pageTitle = 'Subscription Plans';
$pageSubtitle = 'Manage pricing and checkout URLs';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $action = $_POST['action'];
        $planName = $_POST['plan_name'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $checkoutUrl = $_POST['checkout_url'] ?? '';

        if (!$planName || !$checkoutUrl) {
            throw new Exception('All fields are required');
        }

        // Check if plan exists
        $stmt = $pdo->prepare('SELECT id FROM subscription_plans WHERE plan_name = ?');
        $stmt->execute([$planName]);
        $existing = $stmt->fetch();

        if ($action === 'save') {
            if ($existing) {
                // Update existing plan
                $stmt = $pdo->prepare('UPDATE subscription_plans SET price = ?, checkout_url = ?, updated_at = NOW() WHERE plan_name = ?');
                $stmt->execute([$price, $checkoutUrl, $planName]);
                $message = "Plan '{$planName}' updated successfully!";
                $messageType = 'success';
            } else {
                // Insert new plan
                $stmt = $pdo->prepare('INSERT INTO subscription_plans (plan_name, price, checkout_url) VALUES (?, ?, ?)');
                $stmt->execute([$planName, $price, $checkoutUrl]);
                $message = "Plan '{$planName}' created successfully!";
                $messageType = 'success';
            }
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Fetch all plans - with error handling
$plans = [
    'Starter Package' => null,
    'Light Package' => null,
    'Pro Package' => null,
    'Domain Only' => null
];

try {
    $stmt = $pdo->query('SELECT * FROM subscription_plans ORDER BY plan_name');
    $existingPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($existingPlans as $plan) {
        // Update both existing and new plans with database values
        $plans[$plan['plan_name']] = $plan;
    }
} catch (Exception $e) {
    // Table doesn't exist yet - will use default values
    error_log('Subscription plans table not found: ' . $e->getMessage());
}

// Initialize missing plans with default values
$defaultPrices = [
    'Starter Package' => 60,
    'Light Package' => 120,
    'Pro Package' => 200,
    'Domain Only' => 12
];

foreach ($plans as $planName => $planData) {
    if ($planData === null) {
        $plans[$planName] = [
            'id' => null,
            'plan_name' => $planName,
            'price' => $defaultPrices[$planName],
            'checkout_url' => '',
            'created_at' => null,
            'updated_at' => null
        ];
    }
}

// Build page content using output buffering
ob_start();
?>

<!-- Main Content -->
<div class="flex flex-col gap-8">
    <!-- Success/Error Message -->
    <?php if ($message): ?>
        <div
            class="fade-in p-4 rounded-lg flex items-center gap-3 <?php echo $messageType === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400'; ?>">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 fade-in">
        <?php foreach ($plans as $planName => $planData): ?>
            <div class="enhanced-card p-6">
                <!-- Plan Header -->
                <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo $planName; ?></h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        <?php
                        $descriptions = [
                            'Starter Package' => 'Unmanaged Server',
                            'Light Package' => 'Managed Server',
                            'Pro Package' => 'Resellers Special',
                            'Domain Only' => 'Domain Registration'
                        ];
                        echo $descriptions[$planName] ?? '';
                        ?>
                    </p>
                </div>

                <!-- Plan Form -->
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="plan_name" value="<?php echo htmlspecialchars($planName); ?>">

                    <!-- Price Input -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-pound-sign mr-2 text-tc-500"></i>Price (LKR)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">LKR</span>
                            <input type="number" name="price" step="0.01" min="0"
                                value="<?php echo number_format($planData['price'], 2, '.', ''); ?>"
                                class="w-full pl-12 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-tc-500"
                                required>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Current: LKR <span
                                class="font-semibold"><?php echo number_format($planData['price'], 2); ?></span>/year</p>
                    </div>

                    <!-- URL Input -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-link mr-2 text-tc-500"></i>Checkout URL
                        </label>
                        <input type="url" name="checkout_url"
                            value="<?php echo htmlspecialchars($planData['checkout_url']); ?>"
                            placeholder="https://payhere.lk/payment/..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-tc-500 text-sm"
                            required>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">Set your PayHere checkout link</p>
                    </div>

                    <!-- Status Badge -->
                    <div class="pt-2">
                        <?php if ($planData['id']): ?>
                            <span
                                class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 text-xs font-semibold rounded-full">
                                <i class="fas fa-check mr-1"></i>Configured
                            </span>
                        <?php else: ?>
                            <span
                                class="inline-block px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 text-xs font-semibold rounded-full">
                                <i class="fas fa-clock mr-1"></i>Not Set
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Save Button -->
                    <button type="submit"
                        class="w-full mt-6 py-2 px-4 btn-premium text-white font-bold rounded-lg hover:shadow-lg transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>Save Changes
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>