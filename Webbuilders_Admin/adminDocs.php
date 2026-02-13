<?php
$pageTitle = 'Employee Management';
$pageSubtitle = 'Manage employee records and documents';

ob_start();
?>

<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
    transform: scale(0.9);
    transition: transform 0.3s ease;
  }

  .modal.active .modal-content {
    transform: scale(1);
  }

  .dark .modal-content {
    background: #1e293b;
  }

  .close-modal {
    position: absolute;
    right: 1.25rem;
    top: 1.25rem;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    cursor: pointer;
    color: #64748b;
    border-radius: 50%;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    z-index: 10;
    line-height: 0;
    padding-bottom: 4px;
    /* Offset for the &times; character's natural baseline */
  }

  .close-modal:hover {
    color: #ef4444;
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.05);
    transform: rotate(90deg);
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
  }

  .dark .form-label {
    color: #e5e7eb;
  }

  .form-input,
  .form-textarea,
  .form-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: white;
  }

  .dark .form-input,
  .dark .form-textarea,
  .dark .form-select {
    background: #0f172a;
    border-color: #334155;
    color: #e5e7eb;
  }

  .form-input:focus,
  .form-textarea:focus,
  .form-select:focus {
    outline: none;
    border-color: #0ea5a9;
    box-shadow: 0 0 0 3px rgba(14, 165, 169, 0.1);
  }

  .file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
  }

  .file-input-wrapper input[type=file] {
    position: absolute;
    left: -9999px;
  }

  .file-input-label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    background: #f3f4f6;
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
  }

  .dark .file-input-label {
    background: #0f172a;
    border-color: #334155;
  }

  .file-input-label:hover {
    border-color: #0ea5a9;
    background: #f0fdfa;
  }

  .dark .file-input-label:hover {
    background: #134e4a;
  }

  .file-name {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
  }

  .current-doc {
    margin-top: 0rem;
  }

  .current-doc-display {
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: #f0f9fa;
    border-left: 3px solid #0ea5a9;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-container {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
  }

  .employee-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
  }

  .dark .employee-table {
    background: #1e293b;
  }

  .employee-table thead {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: white;
  }

  .employee-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
  }

  .employee-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }

  .dark .employee-table td {
    border-bottom-color: #334155;
  }

  .employee-table tbody tr {
    transition: background 0.3s;
  }

  .employee-table tbody tr:hover {
    background: #f9fafb;
  }

  .dark .employee-table tbody tr:hover {
    background: #0f172a;
  }

  .action-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.3s;
    margin-right: 0.5rem;
  }

  .btn-view {
    background: #3b82f6;
    color: white;
  }

  .btn-view:hover {
    background: #2563eb;
    transform: translateY(-2px);
  }

  .btn-edit {
    background: #f59e0b;
    color: white;
  }

  .btn-edit:hover {
    background: #d97706;
    transform: translateY(-2px);
  }

  .btn-delete {
    background: #ef4444;
    color: white;
  }

  .btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
  }

  .modal-content {
    max-height: 90vh;
    /* Can be up to 90% of the screen height */
    overflow-y: auto;
    /* Scrolling facility if there is a lot of content */

    /* Here is the code to hide the scrollbar */
    -ms-overflow-style: none;
    /* For Internet Explorer and Edge */
    scrollbar-width: none;
    /* For Firefox */
  }

  /* Hiding the scrollbar for Chrome, Safari, and Opera browsers */
  .modal-content::-webkit-scrollbar {
    display: none;
  }

  .btn-more {
    background: #28a745;
    color: white;
  }

  .btn-more:hover {
    background: #218838;
    transform: translateY(-2px);
  }

  .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
  }

  .status-working {
    background: #d1fae5;
    color: #065f46;
  }

  .status-left {
    background: #fee2e2;
    color: #991b1b;
  }

  .dark .status-working {
    background: #065f46;
    color: #d1fae5;
  }

  .dark .status-left {
    background: #991b1b;
    color: #fee2e2;
  }

  .search-filter-container {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }

  .search-box {
    flex: 1;
    min-width: 250px;
  }

  .filter-box {
    min-width: 200px;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .document-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: #f3f4f6;
    border-radius: 8px;
    margin-top: 0.5rem;
  }

  .dark .document-preview {
    background: #0f172a;
  }

  .document-link {
    color: #0ea5a9;
    text-decoration: none;
    font-size: 0.875rem;
  }

  .document-link:hover {
    text-decoration: underline;
  }

  .grid-2 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
  }

  .info-card {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 12px;
    border-left: 4px solid #0ea5a9;
  }

  .dark .info-card {
    background: #0f172a;
  }

  .info-label {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.25rem;
  }

  .info-value {
    font-weight: 600;
    color: #1f2937;
  }

  .dark .info-value {
    color: #e5e7eb;
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

  /* Autocomplete Styles */
  .autocomplete-container {
    position: relative;
    width: 100%;
  }

  .autocomplete-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #d1d5db;
    border-top: none;
    border-radius: 0 0 10px 10px;
    max-height: 250px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }

  .dark .autocomplete-list {
    background: #0f172a;
    border-color: #334155;
  }

  .autocomplete-list.active {
    display: block;
  }

  .autocomplete-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
  }

  .dark .autocomplete-item {
    border-bottom-color: #334155;
  }

  .autocomplete-item:hover {
    background: #f9fafb;
  }

  .dark .autocomplete-item:hover {
    background: #1e293b;
  }

  .autocomplete-item-name {
    font-weight: 600;
    color: #1f2937;
  }

  .dark .autocomplete-item-name {
    color: #e5e7eb;
  }

  .autocomplete-item-nic {
    font-size: 0.875rem;
    color: #6b7280;
  }

  .dark .autocomplete-item-nic {
    color: #9ca3af;
  }

  /* Ensure delete modals are on top of other modals */
  #deleteModal,
  #deleteDocModal {
    z-index: 1000;
  }

  /* 3D Icon Flip Effect */
  .icon-flipper-container {
    perspective: 1000px;
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem auto;
  }

  .icon-flipper {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transform-style: preserve-3d;
  }

  .icon-flipper-container:hover .icon-flipper {
    transform: rotateY(180deg);
  }

  .icon-front,
  .icon-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }

  .icon-back {
    transform: rotateY(180deg);
  }
</style>

<!-- Main Content -->
<div class="enhanced-card fade-in">
  <div class="p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
        <i class="fas fa-users mr-2"></i>Employee Records
      </h2>
      <button onclick="openAddModal()"
        class="btn-premium text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2">
        <i class="fas fa-plus"></i> Add New Employee
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="search-filter-container">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by name or phone number..." class="form-input"
          onkeyup="filterEmployees()">
      </div>
      <div class="filter-box">
        <select id="statusFilter" class="form-select" onchange="filterEmployees()">
          <option value="">All Status</option>
          <option value="working">Working</option>
          <option value="left">Left</option>
        </select>
      </div>
    </div>

    <!-- Employee Table -->
    <div class="table-container">
      <table class="employee-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Joining Date</th>
            <th>Status</th>
            <th>Pending Docs</th>
            <th>Additional document</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="employeeTableBody">
          <tr>
            <td colspan="9" class="text-center py-8 text-gray-500">
              <i class="fas fa-spinner fa-spin text-2xl"></i>
              <p class="mt-2">Loading employees...</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Employee Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeAddModal()">&times;</span>
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
      <i class="fas fa-user-plus mr-2"></i>Add New Employee
    </h2>

    <form id="addEmployeeForm" enctype="multipart/form-data">
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Name *</label>
          <div class="autocomplete-container">
            <input type="text" id="employeeName" name="name" class="form-input" required
              placeholder="Start typing candidate name or click to see all..." oninput="handleNameInput(this.value)"
              onfocus="handleNameFocus()">
            <div id="nameAutocompleteList" class="autocomplete-list"></div>
            <input type="hidden" id="selectedInterviewId" name="selected_interview_id">
            <input type="hidden" id="interviewCvPath" name="interview_cv_path">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="text" id="employeePhone" name="phone_number" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">Designation</label>
          <input type="text" name="designation" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">Joining Date</label>
          <input type="date" name="joining_date" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="working">Working</option>
            <option value="left">Left</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Left Date (if applicable)</label>
        <input type="date" name="left_date" class="form-input">
      </div>

      <div class="form-group">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-textarea" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-textarea" rows="3"></textarea>
      </div>

      <h3 class="text-lg font-semibold mb-4 mt-6 text-gray-800 dark:text-gray-100">Interview & Academic Details</h3>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Interview Date</label>
          <input type="date" name="interview_date" id="interview_date" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">NIC Number</label>
          <input type="text" name="nic_number" id="nic_number" class="form-input"
            placeholder="National Identity Card Number">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Verified By</label>
        <input type="text" name="verified_by" class="form-input" placeholder="Name of verifying officer">
      </div>

      <!-- CV Info Display -->
      <div id="cvInfoBox" class="form-group hidden"
        style="display: none; background: #e0f7fa; border-left: 4px solid #0ea5a9; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <div class="flex items-center gap-2">
          <i class="fas fa-file-pdf text-red-500 text-xl"></i>
          <div>
            <p class="font-semibold text-gray-800 dark:text-gray-100">CV Available from Interview</p>
            <a id="cvLink" href="#" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
              <i class="fas fa-external-link-alt"></i> View CV Document
            </a>
          </div>
        </div>
      </div>

      <h3 class="text-lg font-semibold mb-4 mt-6 text-gray-800 dark:text-gray-100">Documents</h3>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">National ID</label>
          <div class="file-input-wrapper">
            <input type="file" name="national_id" id="national_id" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="national_id" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="national_id_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Character Certificate</label>
          <div class="file-input-wrapper">
            <input type="file" name="character_certificate" id="character_certificate" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="character_certificate" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="character_certificate_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Bank Account Proof</label>
          <div class="file-input-wrapper">
            <input type="file" name="bank_proof" id="bank_proof" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="bank_proof" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="bank_proof_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">CV / Resume</label>
          <div class="file-input-wrapper">
            <input type="file" name="cv_resume" id="cv_resume" accept=".pdf,.doc,.docx" onchange="updateFileName(this)">
            <label for="cv_resume" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="cv_resume_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Appointment Letter</label>
          <div class="file-input-wrapper">
            <input type="file" name="appointment_letter" id="appointment_letter" accept=".pdf,.doc,.docx"
              onchange="updateFileName(this)">
            <label for="appointment_letter" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="appointment_letter_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Employee Photograph</label>
          <div class="file-input-wrapper">
            <input type="file" name="photograph" id="photograph" accept=".jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="photograph" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="photograph_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">OL Result Sheet</label>
          <div class="file-input-wrapper">
            <input type="file" name="ol_result" id="ol_result" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="ol_result" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="ol_result_name"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">AL Result Sheet</label>
          <div class="file-input-wrapper">
            <input type="file" name="al_result" id="al_result" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="al_result" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="al_result_name"></div>
          </div>
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button type="button" onclick="closeAddModal()"
          class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
          Cancel
        </button>
        <button type="submit" class="flex-1 btn-premium text-white py-3 rounded-xl font-semibold">
          <i class="fas fa-save mr-2"></i> Save Employee
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Employee Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeEditModal()">&times;</span>
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
      <i class="fas fa-user-edit mr-2"></i>Edit Employee
    </h2>

    <form id="editEmployeeForm" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Name *</label>
          <input type="text" name="name" id="edit_name" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="text" name="phone_number" id="edit_phone_number" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" id="edit_email" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">Designation</label>
          <input type="text" name="designation" id="edit_designation" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">Joining Date</label>
          <input type="date" name="joining_date" id="edit_joining_date" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" id="edit_status" class="form-select">
            <option value="working">Working</option>
            <option value="left">Left</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Left Date (if applicable)</label>
        <input type="date" name="left_date" id="edit_left_date" class="form-input">
      </div>

      <div class="form-group">
        <label class="form-label">Address</label>
        <textarea name="address" id="edit_address" class="form-textarea" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" id="edit_description" class="form-textarea" rows="3"></textarea>
      </div>

      <h3 class="text-lg font-semibold mb-4 mt-6 text-gray-800 dark:text-gray-100">Interview & Academic Details</h3>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Interview Date</label>
          <input type="date" name="interview_date" id="edit_interview_date" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">NIC Number</label>
          <input type="text" name="nic_number" id="edit_nic_number" class="form-input"
            placeholder="National Identity Card Number">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Verified By</label>
        <input type="text" name="verified_by" id="edit_verified_by" class="form-input"
          placeholder="Name of verifying officer">
      </div>

      <h3 class="text-lg font-semibold mb-4 mt-6 text-gray-800 dark:text-gray-100">Documents (Upload new to replace)
      </h3>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">National ID</label>
          <div class="file-input-wrapper">
            <input type="file" name="national_id" id="edit_national_id" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="edit_national_id" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_national_id_name"></div>
          </div>
          <div class="current-doc" id="edit_national_id_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Character Certificate</label>
          <div class="file-input-wrapper">
            <input type="file" name="character_certificate" id="edit_character_certificate"
              accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this)">
            <label for="edit_character_certificate" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_character_certificate_name"></div>
          </div>
          <div class="current-doc" id="edit_character_certificate_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Bank Account Proof</label>
          <div class="file-input-wrapper">
            <input type="file" name="bank_proof" id="edit_bank_proof" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="edit_bank_proof" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_bank_proof_name"></div>
          </div>
          <div class="current-doc" id="edit_bank_proof_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">CV / Resume</label>
          <div class="file-input-wrapper">
            <input type="file" name="cv_resume" id="edit_cv_resume" accept=".pdf,.doc,.docx"
              onchange="updateFileName(this)">
            <label for="edit_cv_resume" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_cv_resume_name"></div>
          </div>
          <div class="current-doc" id="edit_cv_resume_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Appointment Letter</label>
          <div class="file-input-wrapper">
            <input type="file" name="appointment_letter" id="edit_appointment_letter" accept=".pdf,.doc,.docx"
              onchange="updateFileName(this)">
            <label for="edit_appointment_letter" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_appointment_letter_name"></div>
          </div>
          <div class="current-doc" id="edit_appointment_letter_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Employee Photograph</label>
          <div class="file-input-wrapper">
            <input type="file" name="photograph" id="edit_photograph" accept=".jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="edit_photograph" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_photograph_name"></div>
          </div>
          <div class="current-doc" id="edit_photograph_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">OL Result Sheet</label>
          <div class="file-input-wrapper">
            <input type="file" name="ol_result" id="edit_ol_result" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="edit_ol_result" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_ol_result_name"></div>
          </div>
          <div class="current-doc" id="edit_ol_result_current"></div>
        </div>

        <div class="form-group">
          <label class="form-label">AL Result Sheet</label>
          <div class="file-input-wrapper">
            <input type="file" name="al_result" id="edit_al_result" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateFileName(this)">
            <label for="edit_al_result" class="file-input-label">
              <i class="fas fa-upload mr-2"></i> Choose File
            </label>
            <div class="file-name" id="edit_al_result_name"></div>
          </div>
          <div class="current-doc" id="edit_al_result_current"></div>
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button type="button" onclick="closeEditModal()"
          class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
          Cancel
        </button>
        <button type="submit" class="flex-1 btn-premium text-white py-3 rounded-xl font-semibold">
          <i class="fas fa-save mr-2"></i> Update Employee
        </button>
      </div>
    </form>
  </div>
</div>

<!-- View Employee Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeViewModal()">&times;</span>
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
      <i class="fas fa-user mr-2"></i>Employee Details
    </h2>

    <div id="viewEmployeeContent">
      <!-- Content will be loaded dynamically -->
    </div>

    <div class="mt-6">
      <button onclick="closeViewModal()"
        class="w-full py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
        Close
      </button>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content" style="max-width: 500px;">
    <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
    <div class="text-center">
      <div class="icon-flipper-container">
        <div class="icon-flipper">
          <div class="icon-front bg-red-100 dark:bg-red-900/40">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
          </div>
          <div class="icon-back bg-red-600 text-white shadow-lg">
            <i class="fas fa-trash text-2xl"></i>
          </div>
        </div>
      </div>
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Delete Employee</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-6">
        Are you sure you want to delete this employee? This will also delete all associated documents. This action
        cannot be undone.
      </p>
      <div class="flex gap-3">
        <button onclick="closeDeleteModal()"
          class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
          Cancel
        </button>
        <button onclick="confirmDelete()"
          class="flex-1 py-3 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors font-semibold">
          <i class="fas fa-trash mr-2"></i> Delete
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Additional Document Delete Confirmation Modal -->
<div id="deleteDocModal" class="modal">
  <div class="modal-content" style="max-width: 500px;">
    <span class="close-modal" onclick="closeDeleteDocModal()">&times;</span>
    <div class="text-center">
      <div class="icon-flipper-container">
        <div class="icon-flipper">
          <div class="icon-front bg-red-100 dark:bg-red-900/40">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
          </div>
          <div class="icon-back bg-red-600 text-white shadow-lg">
            <i class="fas fa-trash text-2xl"></i>
          </div>
        </div>
      </div>
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Delete Document</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-6">
        Are you sure you want to delete this additional document? This action cannot be undone.
      </p>
      <div class="flex gap-3">
        <button onclick="closeDeleteDocModal()"
          class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
          Cancel
        </button>
        <button onclick="confirmDeleteDoc()"
          class="flex-1 py-3 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors font-semibold">
          <i class="fas fa-trash mr-2"></i> Delete
        </button>
      </div>
    </div>
  </div>
</div>

<!-- More Employee Modal -->
<div id="moreModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeMoreModal()">&times;</span>
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
      <i class="fas fa-file-medical mr-2"></i>Manage Additional Documents
    </h2>

    <!-- Existing Documents List -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-200">Existing Documents</h3>
      <div id="additionalDocsList" class="max-h-60 overflow-y-auto pr-2">
        <!-- Loaded via JS -->
      </div>
    </div>

    <form id="additionalDocumentsForm" enctype="multipart/form-data"
      class="border-t pt-6 border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200" id="formTitle">Add New Document</h3>
      <input type="hidden" name="id" id="more_id">

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Document name *</label>
          <input type="text" name="document_name" id="document_name" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Published date *</label>
          <input type="date" name="published_date" id="document_published_date" class="form-input" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Additional Document *</label>
        <div class="file-input-wrapper">
          <input type="file" name="additional_document" id="additional_document" accept=".pdf,.jpg,.jpeg,.png"
            onchange="updateFileName(this)" required>
          <label for="additional_document" class="file-input-label">
            <i class="fas fa-upload mr-2"></i> Choose File
          </label>
          <div class="file-name" id="additional_document_name"></div>
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button type="button" onclick="clearMoreModal()"
          class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
          Clear
        </button>
        <button type="submit" class="flex-1 btn-premium text-white py-3 rounded-xl font-semibold">
          <i class="fas fa-save mr-2"></i> Save
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  let employees = [];
  let deleteEmployeeId = null;
  let deleteDocId = null;
  let deleteDocEmpId = null;

  // Load employees on page load
  document.addEventListener('DOMContentLoaded', function () {
    loadEmployees();
  });

  // Load employees from server
  // Load employees from server
  function loadEmployees() {
    console.log('Loading employees...');

    fetch('employee_handler.php?action=fetch')
      .then(response => {
        console.log('Response status:', response.status, response.statusText);
        return response.text();
      })
      .then(text => {
        console.log('Raw response:', text);

        try {
          const data = safeParseJSON(text);
          console.log('Parsed JSON:', data);

          if (data && data.success) {
            employees = data.data;
            displayEmployees(employees);
          } else {
            showError('Failed to load employees: ' + (data?.message || 'Unknown error'));
          }
        } catch (e) {
          console.error('Error processing employees:', e);
          console.log('Response that failed to parse:', text);
          showError('Failed to load employees. Check console for details.');
        }
      })
      .catch(error => {
        console.error('Network error:', error);
        showError('Network error: ' + error.message);
      });
  }


  // Calculate pending documents count for an employee
  function getPendingDocumentsCount(emp) {
    const requiredDocs = [
      'national_id_path',
      'character_certificate_path',
      'bank_proof_path',
      'cv_resume_path',
      'appointment_letter_path',
      'photograph_path',
      'ol_result_path',
      'al_result_path',
      'additional_document'
    ];

    let pendingCount = 0;
    requiredDocs.forEach(docField => {
      if (!emp[docField] || emp[docField].trim() === '') {
        pendingCount++;
      }
    });

    return pendingCount;
  }

  // Display employees in table
  function displayEmployees(data) {
    const tbody = document.getElementById('employeeTableBody');

    if (data.length === 0) {
      tbody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center py-8 text-gray-500">
          <i class="fas fa-users text-4xl mb-2"></i>
          <p>No employees found</p>
        </td>
      </tr>
    `;
      return;
    }

    tbody.innerHTML = data.map(emp => {
      const pendingCount = getPendingDocumentsCount(emp);
      const additionalCount = Number(emp.additional_docs_count || 0);
      const pendingBadgeColor = pendingCount === 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
        pendingCount <= 2 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
          'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
      return `
    <tr>
      <td class="text-gray-800 dark:text-gray-100">${emp.id}</td>
      <td class="text-gray-800 dark:text-gray-100 font-medium">${emp.name}</td>
      <td class="text-gray-600 dark:text-gray-400">${emp.designation || '-'}</td>
      <td class="text-gray-600 dark:text-gray-400">${emp.joining_date || '-'}</td>
      <td>
        <span class="status-badge status-${emp.status}">
          ${emp.status.charAt(0).toUpperCase() + emp.status.slice(1)}
        </span>
      </td>
      <td>
        <span class="px-3 py-1 rounded-full text-sm font-semibold ${pendingBadgeColor}">
          ${pendingCount} pending
        </span>
      </td>
      <td>
        <span onclick="moreEmployee(${emp.id})" class="cursor-pointer px-3 py-1 rounded-full text-sm font-semibold ${additionalCount > 0
          ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
          : 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
        }">
          ${additionalCount} Additional Docs
        </span>
      </td>
      <td class="flex items-center">
        <button onclick="viewEmployee(${emp.id})" class="action-btn btn-view" title="View Details">
          <i class="fas fa-eye"></i>
        </button>
        <button onclick="editEmployee(${emp.id})" class="action-btn btn-edit" title="Edit">
          <i class="fas fa-edit"></i>
        </button>
        <button onclick="deleteEmployee(${emp.id})" class="action-btn btn-delete" title="Delete">
          <i class="fas fa-trash"></i>
        </button>
        <button onclick="moreEmployee(${emp.id})" class="action-btn btn-more" title="More Documents">
          <i class="fas fa-ellipsis-h"></i>
        </button>
      </td>
    </tr>
  `;
    }).join('');
  }

  // Filter employees
  function filterEmployees() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;

    let filtered = employees;

    if (search) {
      filtered = filtered.filter(emp =>
        emp.name.toLowerCase().includes(search) ||
        emp.phone_number.includes(search)
      );
    }

    if (status) {
      filtered = filtered.filter(emp => emp.status === status);
    }

    displayEmployees(filtered);
  }

  // Modal functions
  function openAddModal() {
    document.getElementById('addModal').classList.add('active');
    document.getElementById('addEmployeeForm').reset();
    clearFileNames();
    document.getElementById('nameAutocompleteList').classList.remove('active');
    document.getElementById('selectedInterviewId').value = '';
    // Reset phone and NIC fields when opening modal
    document.getElementById('employeeName').value = '';
    // Hide CV info box
    document.getElementById('cvInfoBox').style.display = 'none';
  }

  function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
  }

  function openEditModal() {
    document.getElementById('editModal').classList.add('active');
  }

  function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
  }

  function openViewModal() {
    document.getElementById('viewModal').classList.add('active');
  }

  function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
  }

  function openDeleteModal() {
    document.getElementById('deleteModal').classList.add('active');
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    deleteEmployeeId = null;
  }

  function openDeleteDocModal() {
    document.getElementById('deleteDocModal').classList.add('active');
  }

  function closeDeleteDocModal() {
    document.getElementById('deleteDocModal').classList.remove('active');
    deleteDocId = null;
    deleteDocEmpId = null;
  }

  function openMoreModal() {
    document.getElementById('moreModal').classList.add('active');
  }

  function closeMoreModal() {
    clearMoreModal();
    document.getElementById('moreModal').classList.remove('active');
  }

  function clearMoreModal() {
    resetMoreForm();

    // Autofocus the Document name field
    const nameInput = document.getElementById('document_name');
    if (nameInput) nameInput.focus();
  }

  // Update file name display
  function updateFileName(input) {
    const nameDiv = document.getElementById(input.id + '_name');
    if (input.files.length > 0) {
      nameDiv.textContent = input.files[0].name;
      nameDiv.style.color = '#0ea5a9';
    } else {
      nameDiv.textContent = '';
    }
  }

  // Clear file names
  function clearFileNames() {
    const fileInputs = ['national_id', 'character_certificate', 'bank_proof', 'cv_resume', 'appointment_letter', 'photograph', 'additional_document'];
    fileInputs.forEach(id => {
      const nameDiv = document.getElementById(id + '_name');
      if (nameDiv) nameDiv.textContent = '';
    });
  }

  // Add employee form submission
  document.getElementById('addEmployeeForm').addEventListener('submit', function (e) {
    e.preventDefault();

    console.log('=== ADDING EMPLOYEE ===');
    const formData = new FormData(this);
    formData.append('action', 'add');

    // Log form data for debugging
    console.log('Form data keys:', Array.from(formData.keys()));
    for (let [key, value] of formData.entries()) {
      if (value instanceof File) {
        console.log(`${key}: File - ${value.name} (${value.size} bytes)`);
      } else {
        console.log(`${key}: ${value}`);
      }
    }

    console.log('Sending POST request to employee_handler.php...');

    fetch('employee_handler.php', {
      method: 'POST',
      body: formData
    })
      .then(response => {
        console.log('Response received:', response.status, response.statusText);
        console.log('Content-Type:', response.headers.get('content-type'));

        if (!response.ok) {
          console.error('HTTP Error:', response.status);
        }

        return response.text();
      })
      .then(text => {
        console.log('Raw response text:', text);
        console.log('Response length:', text.length);

        if (!text) {
          console.error('ERROR: Empty response from server!');
          showError('Server returned empty response. Check console and server logs.');
          return;
        }

        try {
          const data = JSON.parse(text);
          console.log('Parsed JSON:', data);

          if (data.success) {
            showSuccess('Employee added successfully');
            closeAddModal();
            loadEmployees();
          } else {
            showError(data.message || 'Unknown error occurred');
          }
        } catch (e) {
          console.error('JSON Parse Error:', e);
          console.error('Failed to parse response:', text);
          showError('Invalid response format. Check console.');
        }
      })
      .catch(error => {
        console.error('Network/Fetch Error:', error);
        console.error('Error details:', error.stack);
        showError('Failed to add employee: ' + error.message);
      });
  });

  // Edit employee
  function editEmployee(id) {
    fetch(`employee_handler.php?action=get&id=${id}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const emp = data.data;

          document.getElementById('edit_id').value = emp.id;
          document.getElementById('edit_name').value = emp.name;
          document.getElementById('edit_phone_number').value = emp.phone_number;
          document.getElementById('edit_email').value = emp.email || '';
          document.getElementById('edit_designation').value = emp.designation || '';
          document.getElementById('edit_joining_date').value = emp.joining_date || '';
          document.getElementById('edit_left_date').value = emp.left_date || '';
          document.getElementById('edit_status').value = emp.status;
          document.getElementById('edit_address').value = emp.address || '';
          document.getElementById('edit_description').value = emp.description || '';
          document.getElementById('edit_interview_date').value = emp.interview_date || '';
          document.getElementById('edit_nic_number').value = emp.nic_number || '';
          document.getElementById('edit_verified_by').value = emp.verified_by || '';

          // Display current documents
          displayCurrentDocuments(emp);

          openEditModal();
        } else {
          showError('Failed to load employee data');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Failed to load employee data');
      });
  }

  // Display current documents
  function displayCurrentDocuments(emp) {
    const docMappings = [
      { label: 'National ID', path: emp.national_id_path, id: 'edit_national_id_current' },
      { label: 'Character Certificate', path: emp.character_certificate_path, id: 'edit_character_certificate_current' },
      { label: 'Bank Proof', path: emp.bank_proof_path, id: 'edit_bank_proof_current' },
      { label: 'CV/Resume', path: emp.cv_resume_path, id: 'edit_cv_resume_current' },
      { label: 'Appointment Letter', path: emp.appointment_letter_path, id: 'edit_appointment_letter_current' },
      { label: 'Photograph', path: emp.photograph_path, id: 'edit_photograph_current' },
      { label: 'OL Result Sheet', path: emp.ol_result_path, id: 'edit_ol_result_current' },
      { label: 'AL Result Sheet', path: emp.al_result_path, id: 'edit_al_result_current' }
    ];

    docMappings.forEach(doc => {
      const container = document.getElementById(doc.id);
      if (container) {
        if (doc.path) {
          container.innerHTML = `
          <div class="current-doc-display" style="margin-top: 0.5rem; padding: 0.5rem; background: #f0f9fa; border-left: 3px solid #0ea5a9; border-radius: 4px; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-file text-red-500"></i>
            <a href="${doc.path}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
              <i class="fas fa-external-link-alt"></i> View Current
            </a>
          </div>
        `;
        } else {
          container.innerHTML = '';
        }
      }
    });
  }

  // Edit employee form submission
  document.getElementById('editEmployeeForm').addEventListener('submit', function (e) {
    e.preventDefault();

    console.log('=== EDITING EMPLOYEE ===');
    const formData = new FormData(this);
    formData.append('action', 'edit');

    console.log('Form data keys:', Array.from(formData.keys()));
    console.log('Sending POST request to employee_handler.php...');

    fetch('employee_handler.php', {
      method: 'POST',
      body: formData
    })
      .then(response => {
        console.log('Response received:', response.status, response.statusText);
        return response.text();
      })
      .then(text => {
        console.log('Raw response text:', text);

        if (!text) {
          console.error('ERROR: Empty response from server!');
          showError('Server returned empty response.');
          return;
        }

        try {
          const data = JSON.parse(text);
          console.log('Parsed JSON:', data);

          if (data.success) {
            showSuccess('Employee updated successfully');
            closeEditModal();
            loadEmployees();
          } else {
            showError(data.message || 'Unknown error occurred');
          }
        } catch (e) {
          console.error('JSON Parse Error:', e);
          console.error('Failed to parse response:', text);
          showError('Invalid response format.');
        }
      })
      .catch(error => {
        console.error('Fetch Error:', error);
        showError('Failed to update employee: ' + error.message);
      });
  });

  // Additional documents form submission
  document.getElementById('additionalDocumentsForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const docIdInput = document.getElementById('edit_doc_id');
    const isUpdate = !!docIdInput;
    const empId = document.getElementById('more_id').value;

    console.log(isUpdate ? '=== UPDATING ADDITIONAL DOCUMENT ===' : '=== ADDING ADDITIONAL DOCUMENT ===');
    const formData = new FormData(this);
    formData.append('action', isUpdate ? 'update_more' : 'add_more');

    fetch('employee_handler.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.text())
      .then(text => {
        try {
          const data = JSON.parse(text);
          if (data.success) {
            showSuccess(isUpdate ? 'Additional document updated successfully' : 'Additional document saved successfully');
            if (isUpdate) {
              resetMoreForm();
            } else {
              this.reset();
              document.getElementById('more_id').value = empId;
              const fileNameDisplay = document.getElementById('additional_document_name');
              if (fileNameDisplay) fileNameDisplay.textContent = '';
              const today = new Date().toISOString().split('T')[0];
              document.getElementById('document_published_date').value = today;
            }
            loadAdditionalDocuments(empId);
            loadEmployees();
          } else {
            showError(data.message || 'Unknown error occurred');
          }
        } catch (e) {
          console.error('JSON Parse Error:', e, text);
          showError('Invalid server response');
        }
      })
      .catch(error => {
        console.error('Fetch Error:', error);
        showError('Failed to save document');
      });
  });

  // View employee details
  function viewEmployee(id) {
    fetch(`employee_handler.php?action=get&id=${id}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const emp = data.data;

          const content = `
          <div class="grid-2">
            <div class="info-card">
              <div class="info-label">Name</div>
              <div class="info-value">${emp.name}</div>
            </div>
            <div class="info-card">
              <div class="info-label">Phone Number</div>
              <div class="info-value">${emp.phone_number}</div>
            </div>
            <div class="info-card">
              <div class="info-label">Email</div>
              <div class="info-value">${emp.email || '-'}</div>
            </div>
            <div class="info-card">
              <div class="info-label">Designation</div>
              <div class="info-value">${emp.designation || '-'}</div>
            </div>
            <div class="info-card">
              <div class="info-label">Joining Date</div>
              <div class="info-value">${emp.joining_date || '-'}</div>
            </div>
            <div class="info-card">
              <div class="info-label">Status</div>
              <div class="info-value">
                <span class="status-badge status-${emp.status}">
                  ${emp.status.charAt(0).toUpperCase() + emp.status.slice(1)}
                </span>
              </div>
            </div>
            ${emp.left_date ? `
            <div class="info-card">
              <div class="info-label">Left Date</div>
              <div class="info-value">${emp.left_date}</div>
            </div>
            ` : ''}
          </div>
          
          ${emp.address ? `
          <div class="info-card mt-4">
            <div class="info-label">Address</div>
            <div class="info-value">${emp.address}</div>
          </div>
          ` : ''}
          
          ${emp.description ? `
          <div class="info-card mt-4">
            <div class="info-label">Description</div>
            <div class="info-value">${emp.description}</div>
          </div>
          ` : ''}
          
          <h3 class="text-lg font-semibold mt-6 mb-3 text-gray-800 dark:text-gray-100">Interview & Academic Details</h3>
          <div class="grid-2">
            ${emp.interview_date ? `
            <div class="info-card">
              <div class="info-label">Interview Date</div>
              <div class="info-value">${emp.interview_date}</div>
            </div>
            ` : ''}
            ${emp.nic_number ? `
            <div class="info-card">
              <div class="info-label">NIC Number</div>
              <div class="info-value">${emp.nic_number}</div>
            </div>
            ` : ''}
            ${emp.verified_by ? `
            <div class="info-card">
              <div class="info-label">Verified By</div>
              <div class="info-value">${emp.verified_by}</div>
            </div>
            ` : ''}
          </div>
          
          <h3 class="text-lg font-semibold mt-6 mb-3 text-gray-800 dark:text-gray-100">Documents</h3>
          <div class="grid-2">
            <!-- National ID -->
            ${emp.national_id_path ? `
            <div class="document-preview">
              <i class="fas fa-id-card text-gray-500"></i>
              <a href="${emp.national_id_path}" target="_blank" class="document-link">National ID</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-id-card text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">National ID - Pending</span>
            </div>
            `}
            
            <!-- Character Certificate -->
            ${emp.character_certificate_path ? `
            <div class="document-preview">
              <i class="fas fa-certificate text-gray-500"></i>
              <a href="${emp.character_certificate_path}" target="_blank" class="document-link">Character Certificate</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-certificate text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">Character Certificate - Pending</span>
            </div>
            `}
            
            <!-- Bank Proof -->
            ${emp.bank_proof_path ? `
            <div class="document-preview">
              <i class="fas fa-university text-gray-500"></i>
              <a href="${emp.bank_proof_path}" target="_blank" class="document-link">Bank Proof</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-university text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">Bank Proof - Pending</span>
            </div>
            `}
            
            <!-- CV/Resume -->
            ${emp.cv_resume_path ? `
            <div class="document-preview">
              <i class="fas fa-file-alt text-gray-500"></i>
              <a href="${emp.cv_resume_path}" target="_blank" class="document-link">CV/Resume</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-file-alt text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">CV/Resume - Pending</span>
            </div>
            `}
            
            <!-- Appointment Letter -->
            ${emp.appointment_letter_path ? `
            <div class="document-preview">
              <i class="fas fa-file-contract text-gray-500"></i>
              <a href="${emp.appointment_letter_path}" target="_blank" class="document-link">Appointment Letter</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-file-contract text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">Appointment Letter - Pending</span>
            </div>
            `}
            
            <!-- Photograph -->
            ${emp.photograph_path ? `
            <div class="document-preview">
              <i class="fas fa-image text-gray-500"></i>
              <a href="${emp.photograph_path}" target="_blank" class="document-link">Photograph</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-image text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">Photograph - Pending</span>
            </div>
            `}
            
            <!-- OL Result Sheet -->
            ${emp.ol_result_path ? `
            <div class="document-preview">
              <i class="fas fa-graduation-cap text-gray-500"></i>
              <a href="${emp.ol_result_path}" target="_blank" class="document-link">OL Result Sheet</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-graduation-cap text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">OL Result Sheet - Pending</span>
            </div>
            `}
            
            <!-- AL Result Sheet -->
            ${emp.al_result_path ? `
            <div class="document-preview">
              <i class="fas fa-graduation-cap text-gray-500"></i>
              <a href="${emp.al_result_path}" target="_blank" class="document-link">AL Result Sheet</a>
            </div>
            ` : `
            <div class="document-preview" style="background: #fee2e2; border-left: 3px solid #ef4444;">
              <i class="fas fa-graduation-cap text-red-500"></i>
              <span style="color: #dc2626; font-weight: 500;">AL Result Sheet - Pending</span>
            </div>
            `}
          </div>
        `;

          document.getElementById('viewEmployeeContent').innerHTML = content;
          openViewModal();
        } else {
          showError('Failed to load employee data');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Failed to load employee data');
      });
  }

  // Delete employee
  function deleteEmployee(id) {
    deleteEmployeeId = id;
    openDeleteModal();
  }

  // More employee
  function moreEmployee(id) {
    document.getElementById('more_id').value = id;
    loadAdditionalDocuments(id);
    openMoreModal();
  }

  // Load and display additional documents
  function loadAdditionalDocuments(empId) {
    const listContainer = document.getElementById('additionalDocsList');
    listContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

    fetch(`employee_handler.php?action=fetch_more&emp_id=${empId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (data.data.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500 italic text-center py-4">No additional documents found.</p>';
          } else {
            listContainer.innerHTML = data.data.map(doc => `
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mb-2 border-l-4 border-yellow-500">
                <div class="flex-1">
                  <h4 class="font-semibold text-gray-800 dark:text-gray-100">${doc.doc_name}</h4>
                  <p class="text-xs text-gray-500">${doc.date}</p>
                </div>
                <div class="flex gap-2">
                  <a href="${doc.doc_url}" target="_blank" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <button onclick="editAdditionalDoc(${JSON.stringify(doc).replace(/"/g, '&quot;')})" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors" title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button onclick="deleteAdditionalDoc(${doc.id}, ${empId})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            `).join('');
          }
        } else {
          listContainer.innerHTML = '<p class="text-red-500 text-center py-4">Error loading documents.</p>';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        listContainer.innerHTML = '<p class="text-red-500 text-center py-4">Failed to load documents.</p>';
      });
  }

  // Edit additional document
  function editAdditionalDoc(doc) {
    document.getElementById('more_id').value = doc.emp_id;
    // Set a hidden field for the document ID so the backend knows we are updating
    let docIdInput = document.getElementById('edit_doc_id');
    if (!docIdInput) {
      docIdInput = document.createElement('input');
      docIdInput.type = 'hidden';
      docIdInput.name = 'doc_id';
      docIdInput.id = 'edit_doc_id';
      document.getElementById('additionalDocumentsForm').appendChild(docIdInput);
    }
    docIdInput.value = doc.id;

    document.getElementById('document_name').value = doc.doc_name;
    document.getElementById('document_published_date').value = doc.date;

    // Change form title and submit button text
    document.getElementById('formTitle').textContent = 'Edit Document';
    const submitBtn = document.querySelector('#additionalDocumentsForm button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Update';

    // Set requirement to false for file when editing
    document.getElementById('additional_document').required = false;

    // Add a "Cancel Edit" button if it doesn't exist
    if (!document.getElementById('cancelEditBtn')) {
      const cancelBtn = document.createElement('button');
      cancelBtn.type = 'button';
      cancelBtn.id = 'cancelEditBtn';
      cancelBtn.className = 'flex-1 py-3 rounded-xl border border-orange-300 text-orange-600 hover:bg-orange-50 font-semibold transition-colors';
      cancelBtn.textContent = 'Cancel Edit';
      cancelBtn.onclick = function () {
        resetMoreForm();
      };
      submitBtn.parentElement.insertBefore(cancelBtn, submitBtn);
    }
  }

  function resetMoreForm() {
    const form = document.getElementById('additionalDocumentsForm');
    const empId = document.getElementById('more_id').value;
    form.reset();
    document.getElementById('more_id').value = empId;

    const docIdInput = document.getElementById('edit_doc_id');
    if (docIdInput) docIdInput.remove();

    const cancelBtn = document.getElementById('cancelEditBtn');
    if (cancelBtn) cancelBtn.remove();

    document.getElementById('formTitle').textContent = 'Add New Document';
    const submitBtn = document.querySelector('#additionalDocumentsForm button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save';
    document.getElementById('additional_document').required = true;

    // Clear the displayed file name
    const fileNameDisplay = document.getElementById('additional_document_name');
    if (fileNameDisplay) {
      fileNameDisplay.textContent = '';
    }

    // Reset date to today
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('document_published_date');
    if (dateInput) dateInput.value = today;
  }

  // Delete additional document
  function deleteAdditionalDoc(docId, empId) {
    deleteDocId = docId;
    deleteDocEmpId = empId;
    openDeleteDocModal();
  }

  function confirmDeleteDoc() {
    if (!deleteDocId || !deleteDocEmpId) return;

    const formData = new FormData();
    formData.append('action', 'delete_more');
    formData.append('id', deleteDocId);

    fetch('employee_handler.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccess('Document deleted successfully');
          loadAdditionalDocuments(deleteDocEmpId);
          loadEmployees(); // Update counts in table
          closeDeleteDocModal();
        } else {
          showError(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Failed to delete document');
      });
  }

  // Get today's date in 'YYYY-MM-DD' format
  const today = new Date().toISOString().split('T')[0];

  // Assign that date to the input with the specified ID (document_published_date)
  document.getElementById('document_published_date').value = today;

  function confirmDelete() {
    if (!deleteEmployeeId) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deleteEmployeeId);

    fetch('employee_handler.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccess('Employee deleted successfully');
          closeDeleteModal();
          loadEmployees();
        } else {
          showError(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Failed to delete employee');
      });
  }

  // Toast notification system
  function showToast(message, type = 'info') {
    // Create container if it doesn't exist
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    // Create toast element
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

    // Auto remove after 4 seconds
    setTimeout(() => {
      if (toast.parentElement) {
        toast.classList.add('removing');
        setTimeout(() => toast.remove(), 300);
      }
    }, 4000);
  }

  // Show success message
  function showSuccess(message) {
    showToast(message, 'success');
  }

  // Show error message
  function showError(message) {
    showToast(message, 'error');
  }

  // Close modals on outside click
  window.onclick = function (event) {
    const modals = ['addModal', 'editModal', 'viewModal', 'deleteModal', 'moreModal', 'deleteDocModal'];
    modals.forEach(modalId => {
      const modal = document.getElementById(modalId);
      if (event.target === modal) {
        if (modalId === 'moreModal') {
          clearMoreModal();
        }
        modal.classList.remove('active');
      }
    });
  }

  // Safe JSON parser that handles mixed content
  function safeParseJSON(text) {
    try {
      // First try direct parse
      return JSON.parse(text);
    } catch (e) {
      // Try to extract JSON object
      const jsonMatch = text.match(/\{[\s\S]*\}/);
      if (jsonMatch) {
        try {
          return JSON.parse(jsonMatch[0]);
        } catch (e2) {
          console.error('Failed to parse extracted JSON:', e2, 'Text:', text);
          return null;
        }
      }
      console.error('No JSON found in response:', text);
      return null;
    }
  }

  // Autocomplete functions for employee name
  function handleNameFocus() {
    // Show all available candidates when clicking on the field
    handleNameInput(document.getElementById('employeeName').value || '');
  }

  function handleNameInput(value) {
    const autocompleteList = document.getElementById('nameAutocompleteList');

    // Show suggestions if 0 chars (on focus) or 2+ chars (on typing)
    const minChars = value.trim().length > 0 ? 2 : 0;
    if (value.trim().length < minChars) {
      autocompleteList.classList.remove('active');
      return;
    }

    // Fetch suggestions from backend
    fetch(`employee_handler.php?action=suggestions&query=${encodeURIComponent(value)}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
      })
      .then(text => {
        try {
          // Use safe JSON parser
          const data = safeParseJSON(text);
          if (data && data.success && data.data.length > 0) {
            // Build autocomplete list
            autocompleteList.innerHTML = data.data.map(candidate => `
            <div class="autocomplete-item" onclick="selectCandidate(${candidate.id}, '${candidate.candidate_name.replace(/'/g, "\\'")}', '${candidate.nic.replace(/'/g, "\\'")}', '${candidate.phone_number.replace(/'/g, "\\'")}')">
              <div class="autocomplete-item-name">${candidate.candidate_name}</div>
              <div class="autocomplete-item-nic">NIC: ${candidate.nic} | Phone: ${candidate.phone_number}</div>
            </div>
          `).join('');
            autocompleteList.classList.add('active');
          } else {
            autocompleteList.classList.remove('active');
          }
        } catch (e) {
          console.error('Error processing suggestions:', e);
          autocompleteList.classList.remove('active');
        }
      })
      .catch(error => {
        console.error('Error fetching suggestions:', error);
        autocompleteList.classList.remove('active');
      });
  }

  // Select a candidate from autocomplete list
  function selectCandidate(interviewId, candidateName, nic, phoneNumber) {
    document.getElementById('employeeName').value = candidateName;
    document.getElementById('selectedInterviewId').value = interviewId;
    document.getElementById('nameAutocompleteList').classList.remove('active');

    // Auto-fill phone number field
    document.getElementById('employeePhone').value = phoneNumber;

    // Auto-fill NIC number
    document.getElementById('nic_number').value = nic;

    // Fetch complete candidate details and auto-fill form
    fetch(`employee_handler.php?action=candidate&interview_id=${interviewId}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
      })
      .then(text => {
        try {
          // Use safe JSON parser
          const data = safeParseJSON(text);
          if (data && data.success) {
            const candidate = data.data;
            // Auto-fill interview date
            if (candidate.interview_date) {
              document.getElementById('interview_date').value = candidate.interview_date;
            }
            // Auto-fill joining date with interview date if not already set
            const joiningDateInput = document.querySelector('input[name="joining_date"]');
            if (joiningDateInput && !joiningDateInput.value && candidate.interview_date) {
              joiningDateInput.value = candidate.interview_date;
            }
            // Store CV path in hidden field for saving to employees table
            if (candidate.cv_path) {
              document.getElementById('interviewCvPath').value = candidate.cv_path;
              console.log('CV path stored for employee:', candidate.cv_path);
            }
            // Display CV if exists
            if (candidate.cv_path) {
              console.log('CV path available:', candidate.cv_path);
              // Display CV info box
              const cvInfoBox = document.getElementById('cvInfoBox');
              const cvLink = document.getElementById('cvLink');
              cvLink.href = candidate.cv_path;
              cvInfoBox.style.display = 'block';
            } else {
              // Hide CV info if no CV
              document.getElementById('cvInfoBox').style.display = 'none';
            }
          }
        } catch (e) {
          console.error('Error processing candidate details:', e);
        }
      })
      .catch(error => {
        console.error('Error fetching candidate details:', error);
      });
  }

  // Close autocomplete when clicking outside
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.autocomplete-container')) {
      document.getElementById('nameAutocompleteList').classList.remove('active');
    }
  });

</script>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>