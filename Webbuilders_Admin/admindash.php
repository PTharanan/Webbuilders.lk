<?php
require_once 'auth_check.php';
require_once 'config.php';

// Page configuration
$pageTitle = "Dashboard";
$pageSubtitle = "Overview of subscribers and revenue";
$notificationCount = "3";
$includeChartJS = true;


// Page content
ob_start();
?>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
  <div class="stat-card fade-in">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Subscribers</p>
        <h3 class="text-xl lg:text-2xl font-bold mt-1 text-gray-800 dark:text-gray-100">32</h3>
      </div>
      <div class="icon-container bg-tc/10 text-tc">
        <i class="fas fa-users text-lg lg:text-xl"></i>
      </div>
    </div>
    <div class="mt-4 flex items-center text-sm text-green-500">
      <i class="fas fa-users mr-1"></i>
      <span>Active: 28</span>
    </div>
  </div>
  
  <div class="stat-card fade-in" style="animation-delay: 0.1s">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Revenue</p>
        <h3 class="text-xl lg:text-2xl font-bold mt-1 text-gray-800 dark:text-gray-100">Rs 12000</h3>
      </div>
      <div class="icon-container bg-green-500/10 text-green-500">
        <i class="fas fa-dollar-sign text-lg lg:text-xl"></i>
      </div>
    </div>
    <div class="mt-4 flex items-center text-sm text-green-500">
      <i class="fas fa-calendar mr-1"></i>
      <span>This month: Rs 2000</span>
    </div>
  </div>
  
  <div class="stat-card fade-in" style="animation-delay: 0.2s">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Products</p>
        <h3 class="text-xl lg:text-2xl font-bold mt-1 text-gray-800 dark:text-gray-100">15</h3>
      </div>
      <div class="icon-container bg-blue-500/10 text-blue-500">
        <i class="fas fa-box text-lg lg:text-xl"></i>
      </div>
    </div>
    <div class="mt-4 flex items-center text-sm text-blue-500">
      <i class="fas fa-check mr-1"></i>
      <span>Active products</span>
    </div>
  </div>
  
  <div class="stat-card fade-in" style="animation-delay: 0.3s">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Customers</p>
        <h3 class="text-xl lg:text-2xl font-bold mt-1 text-gray-800 dark:text-gray-100">28</h3>
      </div>
      <div class="icon-container bg-purple-500/10 text-purple-500">
        <i class="fas fa-user-friends text-lg lg:text-xl"></i>
      </div>
    </div>
    <div class="mt-4 flex items-center text-sm text-purple-500">
      <i class="fas fa-exclamation-triangle mr-1"></i>
      <span>Expiring: 5</span>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="enhanced-card p-6 text-center fade-in">
    <div class="icon-container bg-tc/10 text-tc mx-auto mb-4">
      <i class="fas fa-users text-2xl"></i>
    </div>
    <h3 class="text-lg font-semibold mb-2">Manage Subscribers</h3>
    <p class="text-gray-600 text-sm mb-4">View and manage all subscribers</p>
    <a href="admin_subcribe.php" class="btn-premium px-4 py-2 rounded-lg text-white font-medium inline-block">View Subscribers</a>
  </div>
  
  <div class="enhanced-card p-6 text-center fade-in" style="animation-delay: 0.1s">
    <div class="icon-container bg-blue-500/10 text-blue-500 mx-auto mb-4">
      <i class="fas fa-box text-2xl"></i>
    </div>
    <h3 class="text-lg font-semibold mb-2">Manage Products</h3>
    <p class="text-gray-600 text-sm mb-4">Add and edit products</p>
    <a href="adminProducts.php" class="btn-premium px-4 py-2 rounded-lg text-white font-medium inline-block">View Products</a>
  </div>
  
  <div class="enhanced-card p-6 text-center fade-in" style="animation-delay: 0.2s">
    <div class="icon-container bg-green-500/10 text-green-500 mx-auto mb-4">
      <i class="fas fa-chart-line text-2xl"></i>
    </div>
    <h3 class="text-lg font-semibold mb-2">Revenue Analytics</h3>
    <p class="text-gray-600 text-sm mb-4">View detailed revenue reports</p>
    <button class="btn-premium px-4 py-2 rounded-lg text-white font-medium">Coming Soon</button>
  </div>
</div>

<?php
$pageContent = ob_get_clean();

// Additional CSS for this page
$additionalCSS = '
.stat-card {
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.icon-container {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.enhanced-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.enhanced-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.btn-premium {
  background: linear-gradient(135deg, #0ea5a9 0%, #2dd4bf 100%);
  transition: all 0.3s ease;
}

.btn-premium:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(14, 165, 169, 0.3);
}

.fade-in {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
';

// Additional JavaScript for this page
$additionalJS = '
// Fade-in animation on scroll
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px"
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.animationPlayState = "running";
    }
  });
}, observerOptions);

document.querySelectorAll(".fade-in").forEach(el => {
  observer.observe(el);
});
';

// Include the layout
include 'layout.php';