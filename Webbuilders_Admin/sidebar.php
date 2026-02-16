<!-- Sidebar -->
<aside class="w-64 sidebar bg-black text-white flex flex-col transition-colors duration-500" id="sidebar">
  <div class="p-6 text-2xl font-bold flex items-center justify-between">
    <img
      src="<?php echo (strpos($_SERVER['PHP_SELF'], '/2checkout/') !== false ? '../' : './'); ?>assets/webbuildersLogo.png"
      alt="Logo" class="ml-2 bg-white h-22 w-auto" />
    <button class="lg:hidden text-white" id="close-sidebar" title="Close sidebar">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <nav class="flex-1 px-4 py-2 overflow-y-auto custom-scrollbar">
    <ul>
      <?php if (($_SESSION['user_role'] ?? 'user') !== 'staff'): ?>
        <li class="my-2"><a href="admindash.php"
            class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'admindash.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
              class="fas fa-tachometer-alt w-5 text-center"></i> Dashboard</a></li>
      <?php endif; ?>

      <li class="my-2"><a href="attendance.php"
          class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
            class="fas fa-id-card w-5 text-center"></i> Attendance</a></li>

      <?php if (($_SESSION['user_role'] ?? 'user') !== 'staff'): ?>
        <li class="my-2"><a href="interviews.php"
            class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'interviews.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
              class="fas fa-id-card w-5 text-center"></i> Interviews</a></li>
      <?php endif; ?>

      <li class="my-2"><a href="adminDocs.php"
          class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'adminDocs.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
            class="fas fa-id-card w-5 text-center"></i> Employees</a></li>

      <?php if (($_SESSION['user_role'] ?? 'user') !== 'staff'): ?>
        <li class="my-2"><a href="adminTeam.php"
            class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'adminTeam.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
              class="fas fa-users w-5 text-center"></i> Team Members</a></li>
        <li class="my-2"><a href="allSubscribers.php"
            class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'allSubscribers.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
              class="fas fa-users w-5 text-center"></i> Subscribers</a></li>
        <li class="my-2"><a href="subscriptionPlan.php"
            class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'subscriptionPlan.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
              class="fas fa-tag w-5 text-center"></i> Subscription Plans</a></li>
        <li class="my-2"><a href="manage_admins.php"
            class="block px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'manage_admins.php' ? 'bg-white/10' : 'hover:bg-white/10'; ?> transition-all duration-300 flex items-center gap-3"><i
              class="fas fa-cog w-5 text-center"></i> Manage Admin</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <div class="p-6 border-t border-white/20">
    <div class="flex items-center space-x-3 mb-4">
      <!-- Profile Image with Fallback -->
      <img
        src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
        alt="User" class="rounded-full border-2 border-white w-10 h-10 object-cover"
        onerror="this.style.display='none'; document.getElementById('avatar-fallback').style.display='flex';">

      <!-- Fallback avatar with initials -->
      <div id="avatar-fallback" class="avatar-placeholder rounded-full border-2 border-white w-10 h-10 hidden">
        AD
      </div>

      <div>
        <span class="font-medium"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
        <p class="text-xs text-white/70"><?= ucfirst($_SESSION['user_role'] ?? 'User') ?></p>
      </div>
    </div>
    <!-- Logout Button in Sidebar -->
    <button id="logout-btn"
      class="w-full mt-4 px-4 py-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all duration-300 flex items-center gap-3 text-white">
      <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
    </button>
  </div>
</aside>

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div
    class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-sm w-full mx-4 transform transition-all duration-300 scale-95 opacity-0"
    id="logout-modal-content">
    <div class="text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
        <i class="fas fa-sign-out-alt text-red-600 dark:text-red-400 text-xl"></i>
      </div>
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Logout</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to logout? You will need to sign in again.
      </p>
      <div class="flex gap-3">
        <button id="cancel-logout"
          class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
          Cancel
        </button>
        <button id="confirm-logout"
          class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
          Logout
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Logout modal functionality
  document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('logout-btn');
    const logoutModal = document.getElementById('logout-modal');
    const logoutModalContent = document.getElementById('logout-modal-content');
    const cancelLogout = document.getElementById('cancel-logout');
    const confirmLogout = document.getElementById('confirm-logout');

    function showModal() {
      logoutModal.classList.remove('hidden');
      setTimeout(() => {
        logoutModalContent.classList.remove('scale-95', 'opacity-0');
        logoutModalContent.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function hideModal() {
      logoutModalContent.classList.remove('scale-100', 'opacity-100');
      logoutModalContent.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        logoutModal.classList.add('hidden');
      }, 300);
    }

    logoutBtn?.addEventListener('click', showModal);
    cancelLogout?.addEventListener('click', hideModal);
    confirmLogout?.addEventListener('click', () => {
      window.location.href = 'logout.php';
    });

    // Close modal when clicking outside
    logoutModal?.addEventListener('click', (e) => {
      if (e.target === logoutModal) hideModal();
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !logoutModal.classList.contains('hidden')) {
        hideModal();
      }
    });
  });
</script>