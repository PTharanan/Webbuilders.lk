<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>

<!-- Logout Confirmation Modal -->
<div class="logout-modal" id="logout-modal">
  <div class="logout-modal-content">
    <div class="text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
        <i class="fas fa-sign-out-alt text-red-500 text-xl"></i>
      </div>
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Logout</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to log out?</p>
      <div class="flex space-x-4">
        <button class="flex-1 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" id="cancel-logout">
          Cancel
        </button>
        <button class="flex-1 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors" id="confirm-logout">
          Logout
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Mobile Top Navbar -->
<div class="lg:hidden mb-6 top-navbar-mobile">
  <div class="mobile-header-content">
    <button class="mobile-menu-btn mr-3 glass-effect p-2 rounded-lg text-gray-800 dark:text-gray-100" id="open-sidebar" title="Open sidebar">
      <i class="fas fa-bars"></i>
    </button>
    <div class="mobile-title-container">
      <h1 class="font-bold font-serif text-gray-800 dark:text-gray-100 elegant-underline"><?php echo $pageTitle ?? 'Admin Dashboard'; ?></h1>
      <p class="text-gray-600 dark:text-gray-400"><?php echo $pageSubtitle ?? 'Manage your admin panel'; ?></p>
    </div>
    <div class="mobile-actions">
      <!-- Dark Mode Toggle in Mobile -->
      <button id="theme-toggle-mobile" class="glass-effect p-2 rounded-lg transition-all duration-300 hover:shadow-lg text-gray-800 dark:text-gray-100">
        🌙
      </button>
      
      <!-- Notification Bell in Mobile -->
      <button class="relative glass-effect p-2 rounded-lg transition-all duration-300 hover:shadow-lg text-gray-800 dark:text-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center"><?php echo $notificationCount ?? '3'; ?></span>
      </button>
      
      <!-- Profile image in mobile navbar -->
      <div class="mobile-profile-container">
        <div class="relative">
          <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
               alt="User" 
               class="rounded-full border-2 border-tc w-8 h-8 object-cover"
               onerror="this.style.display='none'; document.getElementById('mobile-navbar-avatar-fallback').style.display='flex';">
          <div id="mobile-navbar-avatar-fallback" class="avatar-placeholder rounded-full border-2 border-tc w-8 h-8 hidden text-xs">
            AD
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Desktop Top Navbar -->
<div class="hidden lg:flex justify-between items-center mb-8">
  <div class="flex items-center">
    <button class="mobile-menu-btn mr-4 glass-effect p-3 rounded-xl transition-all duration-300 hover:shadow-lg text-gray-800 dark:text-gray-100 lg:hidden" id="open-sidebar-desktop" title="Open sidebar">
      <i class="fas fa-bars"></i>
    </button>
    <div class="page-title">
      <h1 class="text-3xl font-bold font-serif text-gray-800 dark:text-gray-100 elegant-underline inline-block"><?php echo $pageTitle ?? 'Admin Dashboard'; ?></h1>
      <p class="text-gray-600 dark:text-gray-400 mt-2"><?php echo $pageSubtitle ?? 'Manage your admin panel'; ?></p>
    </div>
  </div>
  <div class="flex items-center space-x-4">
    <button id="theme-toggle" class="glass-effect p-3 rounded-xl transition-all duration-300 hover:shadow-lg text-gray-800 dark:text-gray-100">🌙</button>
    <button class="relative glass-effect p-3 rounded-xl transition-all duration-300 hover:shadow-lg text-gray-800 dark:text-gray-100">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
      <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center"><?php echo $notificationCount ?? '3'; ?></span>
    </button>
    <div class="flex items-center space-x-3 glass-effect p-2 rounded-xl">
      <!-- Profile image in top navbar with fallback -->
      <div class="relative">
        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
             alt="User" 
             class="rounded-full border-2 border-tc w-10 h-10 object-cover"
             onerror="this.style.display='none'; document.getElementById('navbar-avatar-fallback').style.display='flex';">
        <div id="navbar-avatar-fallback" class="avatar-placeholder rounded-full border-2 border-tc w-10 h-10 hidden">
          AD
        </div>
      </div>
      <div class="hidden md:block">
        <p class="font-medium text-gray-800 dark:text-gray-100">Admin User</p>
        <p class="text-xs text-gray-600 dark:text-gray-400">Administrator</p>
      </div>
      <!-- Logout Button in Top Navbar (Mobile) -->
      <button id="logout-btn-navbar" class="md:hidden text-gray-600 dark:text-gray-400 hover:text-red-500 transition-colors" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </div>
</div>

<script>
// Theme toggle functionality with persistence
const themeToggle = document.getElementById('theme-toggle');
const themeToggleMobile = document.getElementById('theme-toggle-mobile');

// Load saved theme on page load
function loadTheme() {
  const savedTheme = localStorage.getItem('theme');
  const isDark = savedTheme === 'dark';
  
  if (isDark) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
  
  const icon = isDark ? '☀️' : '🌙';
  if (themeToggle) themeToggle.textContent = icon;
  if (themeToggleMobile) themeToggleMobile.textContent = icon;
}

function toggleTheme() {
  document.documentElement.classList.toggle('dark');
  const isDark = document.documentElement.classList.contains('dark');
  const newIcon = isDark ? '☀️' : '🌙';
  
  // Save theme preference
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  
  if (themeToggle) themeToggle.textContent = newIcon;
  if (themeToggleMobile) themeToggleMobile.textContent = newIcon;
}

// Load theme on page load
loadTheme();

if (themeToggle) {
  themeToggle.addEventListener('click', toggleTheme);
}

if (themeToggleMobile) {
  themeToggleMobile.addEventListener('click', toggleTheme);
}

// Mobile sidebar functionality
const sidebar = document.getElementById('sidebar');
const openSidebarBtn = document.getElementById('open-sidebar');
const openSidebarDesktopBtn = document.getElementById('open-sidebar-desktop');
const closeSidebarBtn = document.getElementById('close-sidebar');
const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

openSidebarBtn.addEventListener('click', function() {
  sidebar.classList.add('active');
  mobileMenuOverlay.classList.add('active');
});

if (openSidebarDesktopBtn) {
  openSidebarDesktopBtn.addEventListener('click', function() {
    sidebar.classList.add('active');
    mobileMenuOverlay.classList.add('active');
  });
}

closeSidebarBtn.addEventListener('click', function() {
  sidebar.classList.remove('active');
  mobileMenuOverlay.classList.remove('active');
});

mobileMenuOverlay.addEventListener('click', function() {
  sidebar.classList.remove('active');
  mobileMenuOverlay.classList.remove('active');
});

// Logout functionality
const logoutModal = document.getElementById('logout-modal');
const logoutBtnSidebar = document.getElementById('logout-btn-sidebar');
const logoutBtnNavbar = document.getElementById('logout-btn-navbar');
const cancelLogout = document.getElementById('cancel-logout');
const confirmLogout = document.getElementById('confirm-logout');

// Show logout modal
function showLogoutModal() {
  logoutModal.classList.add('active');
}

// Hide logout modal
function hideLogoutModal() {
  logoutModal.classList.remove('active');
}

// Logout action
function performLogout() {
  alert('Logging out... Redirecting to login page.');
  // window.location.href = '/login'; // Uncomment this in a real application
}

// Event listeners for logout buttons
logoutBtnSidebar.addEventListener('click', showLogoutModal);
if (logoutBtnNavbar) {
  logoutBtnNavbar.addEventListener('click', showLogoutModal);
}

// Event listeners for logout modal
cancelLogout.addEventListener('click', hideLogoutModal);
confirmLogout.addEventListener('click', performLogout);

// Close modal when clicking outside
logoutModal.addEventListener('click', function(e) {
  if (e.target === logoutModal) {
    hideLogoutModal();
  }
});

// Image error handling - if all external images fail, show fallback avatars
window.addEventListener('load', function() {
  const images = document.querySelectorAll('img');
  images.forEach(img => {
    if (!img.complete || img.naturalHeight === 0) {
      // Image failed to load
      const fallbackId = img.getAttribute('onerror').match(/document\.getElementById\('([^']+)'\)/)[1];
      img.style.display = 'none';
      document.getElementById(fallbackId).style.display = 'flex';
    }
  });
});
</script>