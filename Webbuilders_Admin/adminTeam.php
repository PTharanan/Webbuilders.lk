<?php
require_once 'auth_check.php';
$pageTitle = 'Team Members';
$pageSubtitle = 'Manage your team members with images, names and designations';

ob_start();
?>

<style>
  /* Team Members Grid */
  .team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
  }

  .team-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .dark .team-card {
    background: #1e293b;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  }

  .team-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
  }

  .dark .team-card:hover {
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
  }

  .team-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    background: #f3f4f6;
  }

  .team-info {
    padding: 1.5rem;
  }

  .team-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
  }

  .dark .team-name {
    color: #e5e7eb;
  }

  .team-designation {
    font-size: 0.875rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #0ea5a9 0%, #06b6d4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
  }

  .empty-state-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
  }

  .empty-state-text {
    color: #6b7280;
    margin-bottom: 0.5rem;
  }

  .dark .empty-state-text {
    color: #9ca3af;
  }

  /* Toast Notification Styles */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    pointer-events: none;
  }

  .toast {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    padding: 16px 20px;
    margin-bottom: 10px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.3s ease-out;
    pointer-events: auto;
  }

  .toast-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
  }

  .toast-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .toast-info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
  }

  .toast-icon {
    font-size: 20px;
    flex-shrink: 0;
  }

  .toast-message {
    flex: 1;
    font-weight: 500;
    font-size: 14px;
  }

  .toast-close {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 18px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.7;
    transition: opacity 0.2s;
  }

  .toast-close:hover {
    opacity: 1;
  }

  @keyframes slideIn {
    from {
      transform: translateX(400px);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }

    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }

  .toast.removing {
    animation: slideOut 0.3s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }
</style>

<!-- Main Content -->
<div class="enhanced-card fade-in">
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Team Members</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Active team members</p>
      </div>
    </div>

    <!-- Team Grid -->
    <div id="teamGrid" class="team-grid">
      <div class="empty-state col-span-full">
        <div class="empty-state-icon">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
        <p class="empty-state-text">Loading team members...</p>
      </div>
    </div>
  </div>
</div>

<script>
  // Load team members on page load
  document.addEventListener('DOMContentLoaded', function () {
    loadTeamMembers();
  });

  // Load team members from employees table (only working status)
  function loadTeamMembers() {
    fetch('employee_handler.php?action=fetch_working')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Team members loaded:', data.data);
          displayTeamMembers(data.data);
        } else {
          showToast('Failed to load team members', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
      });
  }

  // Display team members as cards
  function displayTeamMembers(data) {
    const grid = document.getElementById('teamGrid');

    if (data.length === 0) {
      grid.innerHTML = `
      <div class="col-span-full">
        <div class="empty-state">
          <div class="empty-state-icon">
            <i class="fas fa-users"></i>
          </div>
          <p class="empty-state-text">No active team members</p>
        </div>
      </div>
    `;
      return;
    }

    grid.innerHTML = data.map(member => `
    <div class="team-card">
      <img src="${member.photograph_path || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 400%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22400%22 height=%22400%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2250%22 fill=%22%239ca3af%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3E%3C/text%3E%3C/svg%3E'}" alt="${member.name}" class="team-image">
      <div class="team-info">
        <h3 class="team-name">${member.name}</h3>
        <p class="team-designation">${member.designation || 'Team Member'}</p>
      </div>
    </div>
  `).join('');
  }

  // Toast notification system
  function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icons = {
      success: 'fa-check-circle',
      error: 'fa-exclamation-circle',
      info: 'fa-info-circle'
    };

    toast.innerHTML = `
    <i class="fas ${icons[type]} toast-icon"></i>
    <span class="toast-message">${message}</span>
    <button class="toast-close" onclick="this.parentElement.remove()">
      <i class="fas fa-times"></i>
    </button>
  `;

    container.appendChild(toast);

    setTimeout(() => {
      if (toast.parentElement) {
        toast.classList.add('removing');
        setTimeout(() => toast.remove(), 300);
      }
    }, 4000);
  }
</script>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>