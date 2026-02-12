<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Finder - WEBbuilders.lk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">  
    <!-- Main Content -->
    <div class="max-w-11/12 mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="bg-white border-8 border-orange-500 rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row relative">

            <!-- Left Illustration -->
            <div class="absolute hidden xl:flex left-8 top-1/2 mt-32 transform -translate-y-1/2 h-82 w-48 pointer-events-none">
                <img src="assets/domainCheckLeft.png" alt="Left decoration" class="h-full w-full object-contain opacity-90">
            </div>

            <!-- About Section -->
            <div class="p-8 md:p-12 max-w-[99%] md:max-w-[90%] xl:max-w-[70%] flex flex-col mx-auto relative">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">About WEBbuilders.lk</h1>
                
                <div class="text-gray-700 space-y-6 mb-10"> 
                    <p class="text-sm leading-relaxed font-bold">
                        WEBbuilders.lk delivers high-end web solutions to businesses. We provide the most effective class web management service, web designing service, and web development service for your business that helps you to achieve your ventures.
                    </p>
                    
                    <p class="text-sm leading-relaxed font-bold">
                        Our team members have worked with various successful startups and large-scale enterprises to supply the most effective web solutions for several industries. We develop and present new creative website management ideas, web development concepts, web solutions, and approaches for client success.
                        We always focus on critical information, skip irrelevant or unnecessary details, and maintain a top level of professionalism. We believe in dedicated involvement to produce post-implementation support.
                    </p>
                </div>

                <!-- Domain Finder Section -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl p-6 sm:p-8 md:p-12 shadow-lg overflow-hidden relative">                    
                    <!-- Content Container -->
                    <div class="relative z-10 max-w-3xl mx-auto">
                        <div class="text-center mb-6">
                            <h2 class="text-xl md:text-2xl font-bold text-white mb-2 uppercase">Find Your Perfect Domain</h2>
                            <p class="text-sm md:text-base text-orange-100">Check if your dream website name is available</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row mb-4 px-2 sm:px-4">
                            <div class="flex-1 relative">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <input
                                    type="text"
                                    id="domainInput"
                                    placeholder="Enter domain name (e.g., mybusiness)"
                                    class="w-full pl-12 pr-3 sm:pr-4 py-2 sm:py-3 rounded-l-lg border-0 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 text-xs sm:text-sm md:text-base placeholder-gray-500 shadow-md"
                                >
                            </div>
                            <select
                                id="tldSelect"
                                class="px-3 sm:px-4 py-2 sm:py-3 bg-white border-0 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 text-xs sm:text-sm rounded-r-lg shadow-md font-semibold cursor-pointer hover:bg-gray-50 transition duration-300"
                            >
                                <option value=".com">.com</option>
                                <option value=".lk">.lk</option>
                                <option value=".net">.net</option>
                                <option value=".org">.org</option>
                                <option value=".co">.co</option>
                                <option value=".info">.info</option>
                                <option value=".biz">.biz</option>
                                <option value=".io">.io</option>
                                <option value=".app">.app</option>
                                <option value=".dev">.dev</option>
                                <option value=".co.uk">.co.uk</option>
                                <option value=".co.in">.co.in</option>
                                <option value=".us">.us</option>
                                <option value=".ca">.ca</option>
                                <option value=".de">.de</option>
                                <option value=".au">.au</option>
                            </select>
                            <button
                                onclick="checkDomain()"
                                id="checkButton"
                                class="px-4 sm:px-6 py-2 sm:py-3 ml-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-lg transition duration-300 whitespace-nowrap shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                            >
                                <i class="fas fa-search mr-1 sm:mr-2"></i><span class="hidden sm:inline">CHECK AVAILABILITY</span><span class="sm:hidden">CHECK</span>
                            </button>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 justify-center mb-6">
                        </div>
                        
                        <!-- Results Message -->
                        <div id="resultMessage" class="mt-4 p-4 rounded-lg bg-white/10 backdrop-blur-sm hidden animate-fade-in text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <div id="resultIcon" class="text-xl"></div>
                                <div id="resultText" class="text-white font-semibold text-sm md:text-base"></div>
                            </div>
                            <div id="resultActions" class="mt-3 hidden text-center">
                                <button onclick="openRegisterModal()" class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded transition duration-300 mr-2">
                                    <i class="fas fa-shopping-cart mr-1"></i> Register Now
                                </button>
                                <button onclick="checkAnother()" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded transition duration-300">
                                    <i class="fas fa-redo mr-1"></i> Check Another
                                </button>
                            </div>
                        </div>
                        
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="mt-4 text-center hidden">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-white"></div>
                            <p class="text-white mt-2 font-medium text-sm">Checking domain availability...</p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Right Illustration -->
            <div class="absolute hidden xl:flex right-8 top-1/2 transform -translate-y-1/2 h-100 w-56 pointer-events-none">
                <img src="assets/domainCheckRight.png" alt="Right decoration" class="h-full w-full object-contain opacity-90">
            </div>
        </div>
    </div>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        input::placeholder {
            color: #9ca3af;
        }
        
        input:focus::placeholder {
            color: #d1d5db;
        }
    </style>
    
    <script>
        function suggestDomain(domain) {
            document.getElementById('domainInput').value = domain;
            checkDomain();
        }
        
        function checkDomain() {
            const domainInput = document.getElementById('domainInput').value.trim();
            const tldSelect = document.getElementById('tldSelect').value;
            const resultMessage = document.getElementById('resultMessage');
            const resultIcon = document.getElementById('resultIcon');
            const resultText = document.getElementById('resultText');
            const resultActions = document.getElementById('resultActions');
            const checkButton = document.getElementById('checkButton');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const registerLink = document.getElementById('registerLink');
            
            if (!domainInput) {
                showResult('⚠️ Please enter a domain name', '#fbbf24', 'fas fa-exclamation-triangle');
                return;
            }
            
            // Format domain
            let domain = domainInput.toLowerCase().replace(/^https?:\/\//, '').replace(/\/$/, '');
            
            // Extract domain name without path
            domain = domain.split('/')[0];
            
            // Remove any existing extension and add selected TLD
            domain = domain.replace(/\.[a-z]+$/, '') + tldSelect;
            
            // Validate domain format
            const domainRegex = /^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/i;
            if (!domainRegex.test(domain)) {
                showResult('❌ Invalid domain format. Example: example', '#f87171', 'fas fa-times-circle');
                return;
            }
            
            console.log('🔍 Checking domain:', domain);
            
            // Show loading state
            checkButton.disabled = true;
            checkButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> CHECKING...';
            loadingSpinner.classList.remove('hidden');
            resultMessage.classList.add('hidden');
            resultActions.classList.add('hidden');
            
            // Call PHP backend
            fetch('domain_checker_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ domain: domain })
            })
            .then(response => {
                console.log('📡 Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 API Response:', data);
                
                loadingSpinner.classList.add('hidden');
                resultMessage.classList.remove('hidden');
                checkButton.disabled = false;
                checkButton.innerHTML = '<i class="fas fa-search mr-2"></i> CHECK AVAILABILITY';
                
                if (data.success) {
                    if (data.available) {
                        resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-400"></i>';
                        resultText.innerHTML = `✅ <span class="font-bold">${domain}</span> is available!`;
                        resultActions.classList.remove('hidden');
                        registerLink.href = `http://localhost/webbuilders_Admin/domainFinder.php?domain=${encodeURIComponent(domain)}`;
                    } else {
                        resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-400"></i>';
                        resultText.innerHTML = `❌ <span class="font-bold">${domain}</span> is already registered`;
                        
                        // Show suggestions for taken domains
                        setTimeout(() => {
                            const tldSelect = document.getElementById('tldSelect').value;
                            const domainName = domain.split('.')[0];
                            const suggestions = [
                                `my${domainName}${tldSelect}`,
                                `${domainName}online${tldSelect}`,
                                `the${domainName}${tldSelect}`,
                                `${domainName}web${tldSelect}`
                            ];
                            
                            showSuggestions(suggestions);
                        }, 500);
                    }
                } else {
                    resultIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-400"></i>';
                    resultText.innerHTML = `⚠️ ${data.message || 'Unable to check domain'}`;
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                
                loadingSpinner.classList.add('hidden');
                resultMessage.classList.remove('hidden');
                checkButton.disabled = false;
                checkButton.innerHTML = '<i class="fas fa-search mr-2"></i> CHECK AVAILABILITY';
                
                resultIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-400"></i>';
                resultText.textContent = '⚠️ Connection error. Please try again.';
                
                // Fallback: Use DNS check
                setTimeout(() => {
                    fallbackDomainCheck(domain);
                }, 1000);
            });
        }
        
        function fallbackDomainCheck(domain) {
            const resultText = document.getElementById('resultText');
            const resultIcon = document.getElementById('resultIcon');
            
            resultText.innerHTML = '🔄 Using fallback check method...';
            
            // Simple DNS check as fallback
            fetch(`https://dns.google/resolve?name=${domain}&type=A`)
                .then(response => response.json())
                .then(data => {
                    if (data.Answer && data.Answer.length > 0) {
                        // Domain resolves = taken
                        resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-400"></i>';
                        resultText.innerHTML = `❌ <span class="font-bold">${domain}</span> appears to be registered (DNS check)`;
                    } else {
                        // No DNS record = likely available
                        resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-400"></i>';
                        resultText.innerHTML = `✅ <span class="font-bold">${domain}</span> appears to be available (DNS check)`;
                    }
                })
                .catch(() => {
                    // If even fallback fails
                    resultIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-400"></i>';
                    resultText.innerHTML = '⚠️ Please check the domain manually';
                });
        }
        
        function showResult(message, color, icon) {
            const resultMessage = document.getElementById('resultMessage');
            const resultIcon = document.getElementById('resultIcon');
            const resultText = document.getElementById('resultText');
            
            resultIcon.innerHTML = `<i class="${icon}"></i>`;
            resultText.innerHTML = message;
            resultMessage.classList.remove('hidden');
            resultMessage.style.backgroundColor = color + '20';
        }
        
        function showSuggestions(suggestions) {
            const resultMessage = document.getElementById('resultMessage');
            // Remove any existing suggestions first
            const existingSuggestions = resultMessage.querySelector('.suggestions-container');
            if (existingSuggestions) {
                existingSuggestions.remove();
            }
            
            const suggestionsHTML = `
                <div class="suggestions-container mt-6 pt-6 border-t border-white/30">
                    <p class="text-orange-100 text-sm font-semibold mb-4 text-center">💡 Similar available domains:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        ${suggestions.map(domain => 
                            `<button onclick="suggestDomain('${domain}')" class="px-4 py-3 bg-white/15 hover:bg-white/25 text-white rounded-lg text-sm font-medium transition duration-300 border border-white/20 hover:border-white/40 flex items-center justify-center space-x-2 group">
                                <i class="fas fa-globe text-orange-200 group-hover:scale-110 transition duration-200"></i>
                                <span>${domain}</span>
                            </button>`
                        ).join('')}
                    </div>
                </div>
            `;
            
            resultMessage.insertAdjacentHTML('beforeend', suggestionsHTML);
        }
        
        function checkAnother() {
            document.getElementById('domainInput').value = '';
            document.getElementById('domainInput').focus();
            document.getElementById('resultMessage').classList.add('hidden');
            document.getElementById('resultActions').classList.add('hidden');
        }
        
        function openRegisterModal() {
            const domainInput = document.getElementById('domainInput').value.trim();
            const tldSelect = document.getElementById('tldSelect').value;
            let domain = domainInput.toLowerCase().replace(/^https?:\/\//, '').replace(/\/$/, '');
            domain = domain.split('/')[0];
            domain = domain.replace(/\.[a-z]+$/, '') + tldSelect;
            
            openPricingModal(domain);
        }
        
        // Allow Enter key to check domain
        document.getElementById('domainInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                checkDomain();
            }
        });
        
        // Auto-focus on input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('domainInput').focus();
        });
    </script>
    
    <!-- Include Pricing Modal -->
    <?php include 'pricing_modal.php'; ?>
</body>
</html>