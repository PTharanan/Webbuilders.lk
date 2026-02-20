<?php
/**
 * Pricing Page - Domain Registration & Hosting Plans
 * Standalone page without header/footer
 */

// Include database connection
require_once 'includes/dbConnect.php';

// Get domain from URL parameter
$domain = isset($_GET['domain']) ? htmlspecialchars($_GET['domain']) : '';

// Fetch subscription plans from database
$plans = [];
try {
    $stmt = $conn->prepare('SELECT * FROM subscription_plans ORDER BY plan_name');
    $stmt->execute();
    $plansData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($plansData as $plan) {
        $plans[$plan['plan_name']] = $plan;
    }
} catch (Exception $e) {
    error_log('Error fetching plans: ' . $e->getMessage());
}

// Default plan mapping
$planMapping = [
    'Starter Package' => ['key' => 'starter', 'name' => 'Starter Package'],
    'Light Package' => ['key' => 'light', 'name' => 'Light Package'],
    'Pro Package' => ['key' => 'pro', 'name' => 'Pro Package'],
    'Domain Only' => ['key' => 'domainonly', 'name' => 'Domain Only']
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Hosting Plan - WEBbuilders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Header with Back Button -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="home.php"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    <span class="font-medium">Back</span>
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="text-sm text-gray-500">Home</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-900">Choose Plan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Title Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Your cart</h1>
            <div class="flex items-center space-x-2">
                <span class="text-gray-700 font-medium" id="selectedDomainText"><?php echo $domain; ?></span>
                <span class="text-gray-400">•</span>
                <span class="text-sm text-gray-600">Domain registration</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Pricing Cards -->
                    <div class="lg:col-span-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Starter Package -->
                            <div class="border-2 border-gray-200 rounded-xl p-5 hover:shadow-xl hover:border-orange-300 transition-all duration-300 cursor-pointer pricing-card bg-white hover:bg-orange-50"
                                data-plan="starter">
                                <div class="text-center mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter Package</h3>
                                    <p class="text-gray-600 text-sm font-medium">Unmanaged Server</p>
                                </div>
                                <div
                                    class="text-center mb-6 bg-gradient-to-r from-orange-50 to-orange-50 py-4 px-4 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">
                                        <span class="text-sm">LKR</span>
                                        <?php echo isset($plans['Starter Package']) ? number_format($plans['Starter Package']['price'] - (isset($plans['Domain Only']) ? $plans['Domain Only']['price'] : 12), 2) : '60.00'; ?>
                                        <span class="text-base font-semibold text-gray-600">/ Year</span>
                                    </div>
                                </div>
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Domain Registration Support</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>05GB NVMe SSD Storage</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Shared Server</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>03GB Ram</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Single website</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>LiteSpeed + LSCache</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Unlimited Bandwidth</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>FREE SSL Certificates For Life</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>15MB IO</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>300% CPU Power</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>File Access</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-gray-400 mr-3 font-bold text-lg">⊘</span>
                                        <span class="text-gray-400">Error Fixing</span>
                                    </li>
                                </ul>
                                <button
                                    onclick="selectPlan(event, 'starter', <?php echo isset($plans['Starter Package']) ? $plans['Starter Package']['price'] : 60; ?>)"
                                    class="w-full mt-6 py-3 px-4 bg-white border-2 border-orange-500 text-orange-600 font-bold rounded-lg hover:bg-orange-50 transition">
                                    SELECT PLAN
                                </button>
                            </div>

                            <!-- Light Package -->
                            <div class="border-2 border-orange-400 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 cursor-pointer pricing-card bg-white relative overflow-hidden ring-2 ring-orange-300/50"
                                data-plan="light">
                                <div
                                    class="absolute top-0 right-0 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-center py-2 px-8 font-bold text-sm rounded-bl-2xl">
                                    POPULAR
                                </div>
                                <div class="text-center mb-6 mt-4">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Light Package</h3>
                                    <p class="text-gray-600 text-sm font-medium">Managed Server</p>
                                </div>
                                <div
                                    class="text-center mb-6 bg-gradient-to-r from-orange-500 to-orange-600 py-4 px-4 rounded-lg text-white">
                                    <div class="text-2xl font-bold text-white">
                                        <span class="text-sm text-orange-100">LKR</span>
                                        <?php echo isset($plans['Light Package']) ? number_format($plans['Light Package']['price'] - (isset($plans['Domain Only']) ? $plans['Domain Only']['price'] : 12), 2) : '120.00'; ?>
                                        <span class="text-base font-semibold text-orange-100">/ Year</span>
                                    </div>
                                </div>
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Domain Registration Support</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Unlimited SSD Storage</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Shared Server</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>03GB Ram</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>2 website</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>LiteSpeed + LSCache</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Unlimited Bandwidth</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>FREE SSL Certificates For Life</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>15MB IO</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>300% CPU Power</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>File Access</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Error Fixing</span>
                                    </li>
                                </ul>
                                <button
                                    onclick="selectPlan(event, 'light', <?php echo isset($plans['Light Package']) ? $plans['Light Package']['price'] : 120; ?>)"
                                    class="w-full mt-6 py-3 px-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-lg hover:shadow-lg transition">
                                    SELECT PLAN
                                </button>
                            </div>

                            <!-- Pro Package -->
                            <div class="border-2 border-gray-200 rounded-xl p-5 hover:shadow-xl hover:border-orange-300 transition-all duration-300 cursor-pointer pricing-card bg-white hover:bg-orange-50"
                                data-plan="pro">
                                <div class="text-center mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro Package</h3>
                                    <p class="text-gray-600 text-sm font-medium">Resellers Special Server</p>
                                </div>
                                <div
                                    class="text-center mb-6 bg-gradient-to-r from-orange-50 to-orange-50 py-4 px-4 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">
                                        <span class="text-sm">LKR</span>
                                        <?php echo isset($plans['Pro Package']) ? number_format($plans['Pro Package']['price'] - (isset($plans['Domain Only']) ? $plans['Domain Only']['price'] : 12), 2) : '200.00'; ?>
                                        <span class="text-base font-semibold text-gray-600">/ Year</span>
                                    </div>
                                </div>
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Domain Registration Support</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>25GB NVMe SSD Storage</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Shared Server</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>03GB Ram</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Unlimited Website</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>LiteSpeed + LSCache</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Unlimited Bandwidth</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>FREE SSL Certificates For Life</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>15MB IO</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>300% CPU Power</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>File Access</span>
                                    </li>
                                    <li class="flex items-start hover:translate-x-1 transition-transform">
                                        <span class="text-orange-500 mr-3 font-bold text-lg">✓</span>
                                        <span>Error Fixing</span>
                                    </li>
                                </ul>
                                <button
                                    onclick="selectPlan(event, 'pro', <?php echo isset($plans['Pro Package']) ? $plans['Pro Package']['price'] : 200; ?>)"
                                    class="w-full mt-6 py-3 px-4 bg-white border-2 border-orange-500 text-orange-600 font-bold rounded-lg hover:bg-orange-50 transition">
                                    SELECT PLAN
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar - Order Summary -->
                    <div class="lg:col-span-1">
                        <div
                            class="sticky top-6 bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 rounded-xl p-5 h-fit shadow-md">
                            <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-receipt mr-2 text-orange-500"></i>Order Summary
                            </h4>

                            <!-- Domain -->
                            <div class="border-b-2 border-gray-300 pb-4 mb-4">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Domain
                                    Registration</p>
                                <p class="text-lg font-bold text-gray-900" id="selectedDomainSummary">
                                    <?php echo $domain; ?>
                                </p>
                                <p class="text-sm text-orange-600 font-semibold mt-1">LKR
                                    <?php echo isset($plans['Domain Only']) ? number_format($plans['Domain Only']['price'], 2) : '12.00'; ?>
                                    / Year
                                </p>
                            </div>

                            <!-- Package -->
                            <div id="selectedPackageSection" class="border-b-2 border-gray-300 pb-4 mb-4 hidden">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Selected
                                    Package</p>
                                <p class="text-lg font-bold text-gray-900" id="selectedPackageName">None</p>
                                <p class="text-sm  text-orange-600 mt-1">LKR <span id="selectedPackagePrice">0</span>.00
                                    / Year</p>
                            </div>

                            <!-- Total -->
                            <div class="bg-white border-2 border-orange-300 rounded-lg p-4 mb-6 shadow-sm">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Package:</p>
                                    <p class="font-bold text-gray-900">LKR <span id="summaryPackagePrice">0</span>.00
                                    </p>
                                </div>
                                <div class="flex justify-between items-center border-t-2 border-gray-200 pt-3 mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Domain:</p>
                                    <p class="font-bold text-gray-900">LKR
                                        <?php echo isset($plans['Domain Only']) ? number_format($plans['Domain Only']['price'], 2) : '12.00'; ?>
                                    </p>
                                </div>
                                <div class="flex justify-between items-center border-t-2 border-orange-300 pt-3">
                                    <p class="font-bold text-gray-900 text-sm">Total Amount:</p>
                                    <p class="font-bold text-orange-600">LKR <span
                                            id="totalAmount"><?php echo isset($plans['Domain Only']) ? $plans['Domain Only']['price'] : 12; ?></span>.00
                                    </p>
                                </div>
                            </div>

                            <!-- Proceed Button -->
                            <button onclick="proceedToCheckout()"
                                class="w-full py-3 px-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold rounded-lg transition duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-lock mr-2"></i>Proceed to Payment
                            </button>

                            <p class="text-xs text-gray-600 text-center mt-4">✓ Secure payment processing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

    <script>
        let selectedDomain = '<?php echo addslashes($domain); ?>';
        let selectedPlan = '';
        let selectedPlanPrice = 0;
        let checkoutUrl = '';
        let domainPrice = <?php echo isset($plans['Domain Only']) ? $plans['Domain Only']['price'] : 12; ?>;

        // Store plan checkout URLs from PHP
        const planCheckoutUrls = {
            'starter': '<?php echo isset($plans['Starter Package']) ? addslashes($plans['Starter Package']['checkout_url']) : '#'; ?>',
            'light': '<?php echo isset($plans['Light Package']) ? addslashes($plans['Light Package']['checkout_url']) : '#'; ?>',
            'pro': '<?php echo isset($plans['Pro Package']) ? addslashes($plans['Pro Package']['checkout_url']) : '#'; ?>',
            'domainonly': '<?php echo isset($plans['Domain Only']) ? addslashes($plans['Domain Only']['checkout_url']) : '#'; ?>'
        };

        function selectPlan(event, plan, price) {
            if (event) {
                event.stopPropagation();
            }
            selectedPlan = plan;
            selectedPlanPrice = price;
            checkoutUrl = planCheckoutUrls[plan] || '#';

            // Show package section when plan is selected
            if (plan !== 'domainonly') {
                document.getElementById('selectedPackageSection').classList.remove('hidden');
            } else {
                document.getElementById('selectedPackageSection').classList.add('hidden');
            }

            // Update card highlights
            document.querySelectorAll('.pricing-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-orange-500', 'shadow-xl');
            });
            if (plan !== 'domainonly' && event && event.currentTarget) {
                event.currentTarget.closest('.pricing-card')?.classList.add('ring-2', 'ring-orange-500', 'shadow-xl');
            }

            // Update summary
            const planNames = {
                'starter': 'Starter Package',
                'light': 'Light Package',
                'pro': 'Pro Package',
                'domainonly': 'Domain Only'
            };

            // Calculate package amount only (subtract domain price from total)
            const packageAmountOnly = plan !== 'domainonly' ? (price - domainPrice) : price;

            document.getElementById('selectedPackageName').textContent = planNames[plan];
            document.getElementById('selectedPackagePrice').textContent = packageAmountOnly;
            document.getElementById('summaryPackagePrice').textContent = packageAmountOnly;

            // Calculate total
            const total = plan === 'domainonly' ? domainPrice : (packageAmountOnly + domainPrice);
            document.getElementById('totalAmount').textContent = total;
        }

        async function proceedToCheckout() {
            if (!selectedDomain) {
                alert('Please select a domain');
                return;
            }

            // If no plan selected, default to domain only
            if (!selectedPlan) {
                selectPlan(null, 'domainonly', domainPrice);
            }

            if (!checkoutUrl || checkoutUrl === '#') {
                alert('Checkout URL not configured for this plan. Please contact support.');
                return;
            }

            // Prepare checkout data
            const checkoutData = {
                domain: selectedDomain,
                plan: selectedPlan || 'domainonly',
                planPrice: (selectedPlan && selectedPlan !== 'domainonly') ? (selectedPlanPrice - domainPrice) : 0,
                domainPrice: domainPrice,
                total: selectedPlan === 'domainonly' ? domainPrice : (selectedPlanPrice || domainPrice),
                payment_id: 'PENDING',
                order_id: 'WEB-' + Date.now()
            };

            // Store in local storage as backup
            localStorage.setItem('checkoutData', JSON.stringify(checkoutData));

            // Show loading state on button
            const btn = event?.target?.closest('button') || document.querySelector('button[onclick="proceedToCheckout()"]');
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

            try {
                console.log('Saving checkout data to DB before redirect:', checkoutData);
                const response = await fetch('save_checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(checkoutData)
                });

                const result = await response.json();
                console.log('Initial save result:', result);

                // Redirect to payment URL with custom parameters for tracking
                const separator = checkoutUrl.includes('?') ? '&' : '?';
                const finalUrl = checkoutUrl + separator + 'order_id=' + checkoutData.order_id + '&custom_1=' + encodeURIComponent(selectedDomain);
                window.location.href = finalUrl;
            } catch (error) {
                console.error('Error in initial save:', error);
                // Even if save fails, redirect anyway so user can pay
                window.location.href = checkoutUrl;
            }
        }

        // Initialize domain display on page load
        if (selectedDomain) {
            document.getElementById('selectedDomainText').textContent = selectedDomain;
            document.getElementById('selectedDomainSummary').textContent = selectedDomain;
        }
    </script>
</body>

</html>