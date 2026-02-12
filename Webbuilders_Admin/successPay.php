<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - WEBbuilders.lk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Success Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-12 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full mb-6 animate-bounce-slow">
                    <i class="fas fa-check text-green-500 text-5xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-3">Payment Successful!</h1>
                <p class="text-green-100 text-lg">Your order has been confirmed</p>
            </div>

            <!-- Order Details -->
            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-receipt text-orange-500 mr-3"></i>
                        Order Summary
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Domain -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Domain Registration</p>
                                <p class="text-lg font-bold text-gray-900" id="orderDomain">-</p>
                            </div>
                            <span class="text-gray-900 font-semibold">$12.00</span>
                        </div>

                        <!-- Package -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Hosting Package</p>
                                <p class="text-lg font-bold text-gray-900" id="orderPackage">-</p>
                            </div>
                            <span class="text-gray-900 font-semibold">$<span id="orderPackagePrice">0</span>.00</span>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center pt-3">
                            <p class="text-xl font-bold text-gray-900">Total Paid</p>
                            <p class="text-3xl font-bold text-green-600">$<span id="orderTotal">0</span>.00</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                        <div>
                            <h3 class="font-bold text-blue-900 mb-1">What's Next?</h3>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• You will receive a confirmation email shortly</li>
                                <li>• Your domain will be registered within 24 hours</li>
                                <li>• Hosting account details will be sent to your email</li>
                                <li>• Our support team will contact you for setup assistance</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Payment Method</p>
                        <p class="text-sm font-bold text-gray-900">PayHere Gateway</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Transaction Date</p>
                        <p class="text-sm font-bold text-gray-900" id="transactionDate">-</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="domainFinder.php" class="flex-1 py-3 px-6 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold rounded-lg transition duration-300 text-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                    <button onclick="window.print()" class="flex-1 py-3 px-6 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition duration-300 shadow-md">
                        <i class="fas fa-print mr-2"></i>Print Receipt
                    </button>
                </div>

                <!-- Support -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 mb-2">Need help? Contact our support team</p>
                    <div class="flex justify-center gap-4 text-sm">
                        <a href="mailto:support@webbuilders.lk" class="text-orange-600 hover:text-orange-700 font-semibold">
                            <i class="fas fa-envelope mr-1"></i>support@webbuilders.lk
                        </a>
                        <span class="text-gray-400">|</span>
                        <a href="tel:+94123456789" class="text-orange-600 hover:text-orange-700 font-semibold">
                            <i class="fas fa-phone mr-1"></i>+94 123 456 789
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Badge -->
        <div class="text-center mt-6 text-gray-600 text-sm">
            <i class="fas fa-shield-alt text-green-500 mr-2"></i>
            Your payment was processed securely through PayHere
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceSlow {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-bounce-slow {
            animation: bounceSlow 2s infinite;
        }

        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none;
            }
        }
    </style>

    <script>
        // Retrieve checkout data from localStorage
        const checkoutData = localStorage.getItem('checkoutData');
        
        if (checkoutData) {
            const data = JSON.parse(checkoutData);
            
            // Populate order details
            document.getElementById('orderDomain').textContent = data.domain || '-';
            
            const planNames = {
                'starter': 'Starter Package',
                'light': 'Light Package',
                'pro': 'Pro Package',
                'domainonly': 'Domain Only'
            };
            
            document.getElementById('orderPackage').textContent = planNames[data.plan] || data.plan || '-';
            document.getElementById('orderPackagePrice').textContent = data.planPrice || 0;
            document.getElementById('orderTotal').textContent = data.total || 12;
            
            console.log('Order Details:', data);
        } else {
            // No checkout data found
            console.warn('No checkout data found in localStorage');
        }
        
        // Set transaction date
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('transactionDate').textContent = dateStr;

        // Get PayHere payment details from URL parameters (if available)
        const urlParams = new URLSearchParams(window.location.search);
        const paymentId = urlParams.get('payment_id');
        const orderId = urlParams.get('order_id');
        
        if (paymentId) {
            console.log('PayHere Payment ID:', paymentId);
        }
        if (orderId) {
            console.log('Order ID:', orderId);
        }
    </script>
</body>
</html>
