<?php
require_once 'auth_check.php';
require_once 'config.php';

$pageTitle = 'Interview Management';
$pageSubtitle = 'Manage and track candidate interviews';

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
    max-width: 700px;
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
    right: 1.5rem;
    top: 1.5rem;
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
    transition: color 0.3s;
  }
  
  .close-modal:hover {
    color: #ef4444;
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
  
  .form-input, .form-textarea, .form-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: white;
  }
  
  .dark .form-input, .dark .form-textarea, .dark .form-select {
    background: #0f172a;
    border-color: #334155;
    color: #e5e7eb;
  }
  
  .form-input:focus, .form-textarea:focus, .form-select:focus {
    outline: none;
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
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
    border-color: #f97316;
    background: #fff7ed;
  }
  
  .dark .file-input-label:hover {
    background: #7c2d12;
  }
  
  .file-name {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
  }
  
  .hidden-field {
    display: none;
  }

  .table-container {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
  }
  
  .interview-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
  }
  
  .dark .interview-table {
    background: #1e293b;
  }
  
  .interview-table thead {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: white;
  }
  
  .interview-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
  }
  
  .interview-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .dark .interview-table td {
    border-bottom-color: #334155;
  }
  
  .interview-table tbody tr {
    transition: background 0.3s;
  }
  
  .interview-table tbody tr:hover {
    background: #f9fafb;
  }
  
  .dark .interview-table tbody tr:hover {
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
  
  .status-badge {
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-block;
  }
  
  .status-pending {
    background: #fef3c7;
    color: #92400e;
  }
  
  .status-passed {
    background: #d1fae5;
    color: #065f46;
  }
  
  .status-rejected {
    background: #fee2e2;
    color: #991b1b;
  }

  .btn-add-interview {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-add-interview:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
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
  
  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  /* Confirmation Modal */
  .confirm-modal {
    display: none;
    position: fixed;
    z-index: 101;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
  }

  .confirm-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
  }

  .confirm-content {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
    transform: scale(0.9);
    transition: transform 0.3s ease;
  }

  .confirm-modal.active .confirm-content {
    transform: scale(1);
  }

  .dark .confirm-content {
    background: #1e293b;
  }

  .confirm-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: #1f2937;
  }

  .dark .confirm-title {
    color: #e5e7eb;
  }

  .confirm-message {
    color: #6b7280;
    margin-bottom: 1.5rem;
  }

  .dark .confirm-message {
    color: #9ca3af;
  }

  .confirm-buttons {
    display: flex;
    gap: 1rem;
  }

  .btn-confirm-cancel, .btn-confirm-delete {
    flex: 1;
    padding: 0.75rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
  }

  .btn-confirm-cancel {
    background: #e5e7eb;
    color: #1f2937;
  }

  .btn-confirm-cancel:hover {
    background: #d1d5db;
  }

  .btn-confirm-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .btn-confirm-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
  }

  .grid-2 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
  }

  @media (max-width: 768px) {
    .modal-content {
      padding: 1.5rem;
    }
    
    .interview-table {
      font-size: 0.875rem;
    }
    
    .interview-table th, .interview-table td {
      padding: 0.75rem;
    }
    
    .action-btn {
      padding: 0.4rem 0.6rem;
      font-size: 0.75rem;
      margin-right: 0.25rem;
    }
  }
</style>

<!-- Add Interview Button -->
<div class="flex justify-between items-center mb-6">
  <div></div>
  <button onclick="openAddModal()" class="btn-add-interview">
    <i class="fas fa-plus"></i> Add Interview
  </button>
</div>

<!-- Interview Table -->
<div class="table-container">
  <table class="interview-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>NIC</th>
        <th>Phone</th>
        <th>Interview Date</th>
        <th>Status</th>
        <th>CV</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="interviewTableBody">
      <tr>
        <td colspan="7" style="text-align: center; padding: 2rem; color: #9ca3af;">
          <i class="fas fa-inbox text-3xl mb-2 block"></i>
          No interviews found. Click "Add Interview" to create one.
        </td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Add/Edit Interview Modal -->
<div id="interviewModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeAddModal()">&times;</span>
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; color: #1f2937;" class="dark:text-white" id="modalTitle">Add New Interview</h2>
    
    <form id="interviewForm">
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Candidate Name *</label>
          <input type="text" id="candidateName" class="form-input" placeholder="Enter full name" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">NIC *</label>
          <input type="text" id="candidateNIC" class="form-input" placeholder="Enter NIC" required>
        </div>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="tel" id="candidatePhone" class="form-input" placeholder="Enter phone number" required>
        </div>

        <div class="form-group">
          <label class="form-label">Interview Date *</label>
          <input type="date" id="interviewDate" class="form-input" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" id="cvLabel">CV Document <span id="cvRequired">*</span></label>
        <div class="file-input-wrapper">
          <input type="file" id="cvDocument" accept=".pdf,.doc,.docx">
          <label for="cvDocument" class="file-input-label">
            <span><i class="fas fa-cloud-upload-alt" style="margin-right: 0.5rem;"></i> Click to upload CV (PDF, DOC, DOCX)</span>
          </label>
        </div>
        <div id="cvFileName" class="file-name"></div>
        <div id="existingCvInfo" class="file-name" style="color: #10b981; display: none; margin-top: 0.5rem;">
          <i class="fas fa-check-circle"></i> Existing CV will be kept if you don't upload a new one
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Interview Status *</label>
        <select id="interviewStatus" class="form-select" required onchange="updateStatusFields()">
          <option value="">-- Select Status --</option>
          <option value="pending">Pending</option>
          <option value="passed">Passed</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <div id="joinDateField" class="form-group hidden-field">
        <label class="form-label">Join Date (for passed candidates)</label>
        <input type="date" id="joinDate" class="form-input">
      </div>

      <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="button" onclick="closeAddModal()" class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
          Cancel
        </button>
        <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg transition-all font-semibold">
          <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Save Interview
        </button>
      </div>
    </form>
  </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeViewModal()">&times;</span>
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; color: #1f2937;" class="dark:text-white">Interview Details</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
      <div>
        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">Candidate Name</p>
        <p id="viewCandidateName" style="font-size: 1.1rem; font-weight: 600; color: #1f2937;" class="dark:text-white"></p>
      </div>
      <div>
        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">NIC</p>
        <p id="viewCandidateNIC" style="font-size: 1.1rem; font-weight: 600; color: #1f2937;" class="dark:text-white"></p>
      </div>
      <div>
        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">Phone Number</p>
        <p id="viewCandidatePhone" style="font-size: 1.1rem; font-weight: 600; color: #1f2937;" class="dark:text-white"></p>
      </div>
      <div>
        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">Interview Date</p>
        <p id="viewInterviewDate" style="font-size: 1.1rem; font-weight: 600; color: #1f2937;" class="dark:text-white"></p>
      </div>
    </div>

    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;" class="dark:border-gray-600">
      <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;" class="dark:text-gray-400">Status</p>
      <div id="viewStatusBadge" style="display: inline-block; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem;"></div>
    </div>

    <div id="viewJoinDateSection" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; display: none;" class="dark:border-gray-600">
      <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;" class="dark:text-gray-400">Join Date</p>
      <p id="viewJoinDate" style="font-size: 1.1rem; font-weight: 600; color: #1f2937;" class="dark:text-white"></p>
    </div>

    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;" class="dark:border-gray-600">
      <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;" class="dark:text-gray-400">CV Document</p>
      <div id="viewCVSection" style="font-size: 1.1rem; font-weight: 600;"></div>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
      <button type="button" onclick="closeViewModal()" class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-semibold">
        Close
      </button>
      <button type="button" onclick="editFromView()" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg transition-all font-semibold">
        <i class="fas fa-edit" style="margin-right: 0.5rem;"></i> Edit
      </button>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="confirmModal" class="confirm-modal">
  <div class="confirm-content">
    <div style="text-align: center;">
      <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
    </div>
    <h3 class="confirm-title">Delete Interview</h3>
    <p class="confirm-message">Are you sure you want to delete this interview record? This action cannot be undone.</p>
    <div class="confirm-buttons">
      <button type="button" class="btn-confirm-cancel" onclick="closeConfirmModal()">Cancel</button>
      <button type="button" class="btn-confirm-delete" onclick="confirmDelete()">Delete</button>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>

<script>
let currentEditId = null;
let interviews = [];

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  loadInterviews();
  
  document.getElementById('interviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    saveInterview();
  });

  document.getElementById('cvDocument').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'No file chosen';
    document.getElementById('cvFileName').textContent = '✓ ' + fileName;
    document.getElementById('cvFileName').style.color = '#10b981';
  });

  // Close modal when clicking outside
  document.getElementById('interviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
  });

  document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
  });

  document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeViewModal();
  });
});

function openAddModal() {
  currentEditId = null;
  document.getElementById('modalTitle').textContent = 'Add New Interview';
  document.getElementById('interviewForm').reset();
  document.getElementById('cvFileName').textContent = '';
  document.getElementById('cvRequired').style.display = 'inline';
  document.getElementById('existingCvInfo').style.display = 'none';
  document.getElementById('cvDocument').required = true;
  document.getElementById('joinDateField').classList.add('hidden-field');
  document.getElementById('interviewModal').classList.add('active');
}

function closeAddModal() {
  document.getElementById('interviewModal').classList.remove('active');
  currentEditId = null;
}

function closeViewModal() {
  document.getElementById('viewModal').classList.remove('active');
}

function viewInterview(id) {
  fetch(`interview_handler.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const interview = data.data;
        document.getElementById('viewCandidateName').textContent = interview.candidate_name;
        document.getElementById('viewCandidateNIC').textContent = interview.nic;
        document.getElementById('viewCandidatePhone').textContent = interview.phone_number;
        document.getElementById('viewInterviewDate').textContent = formatDate(interview.interview_date);
        
        // Status badge
        let statusClass = '';
        if (interview.status === 'pending') statusClass = 'status-pending';
        else if (interview.status === 'passed') statusClass = 'status-passed';
        else if (interview.status === 'rejected') statusClass = 'status-rejected';
        document.getElementById('viewStatusBadge').className = 'status-badge ' + statusClass;
        document.getElementById('viewStatusBadge').textContent = capitalizeFirst(interview.status);
        
        // Join date (only show if passed)
        const joinDateSection = document.getElementById('viewJoinDateSection');
        if (interview.status === 'passed' && interview.join_date) {
          joinDateSection.style.display = 'block';
          document.getElementById('viewJoinDate').textContent = formatDate(interview.join_date);
        } else {
          joinDateSection.style.display = 'none';
        }
        
        // CV section
        const cvSection = document.getElementById('viewCVSection');
        if (interview.cv_path) {
          cvSection.innerHTML = `<a href="${interview.cv_path}" target="_blank" class="text-blue-500 hover:text-blue-700"><i class="fas fa-file-pdf"></i>View CV</a>`;
        } else {
          cvSection.textContent = 'No file';
          cvSection.style.color = '#9ca3af';
        }
        
        // Store the ID for edit function
        window.currentViewId = id;
        
        document.getElementById('viewModal').classList.add('active');
      } else {
        showToast('Failed to load interview details', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('An error occurred while loading interview details', 'error');
    });
}

function editFromView() {
  closeViewModal();
  editInterview(window.currentViewId);
}

function updateStatusFields() {
  const status = document.getElementById('interviewStatus').value;
  const joinDateField = document.getElementById('joinDateField');
  
  if (status === 'passed') {
    joinDateField.classList.remove('hidden-field');
    document.getElementById('joinDate').required = true;
  } else {
    joinDateField.classList.add('hidden-field');
    document.getElementById('joinDate').required = false;
    document.getElementById('joinDate').value = '';
  }
}

function saveInterview() {
  const form = document.getElementById('interviewForm');
  const formData = new FormData(form);
  
  const candidateName = document.getElementById('candidateName').value;
  const nic = document.getElementById('candidateNIC').value;
  const phone = document.getElementById('candidatePhone').value;
  const interviewDate = document.getElementById('interviewDate').value;
  const status = document.getElementById('interviewStatus').value;
  const joinDate = document.getElementById('joinDate').value;
  const cvFile = document.getElementById('cvDocument');

  // Validation
  if (!candidateName || !nic || !phone || !interviewDate || !status) {
    showToast('Please fill all required fields', 'error');
    return;
  }

  // CV is required only when adding (not when editing)
  if (!currentEditId && cvFile.files.length === 0) {
    showToast('Please upload a CV document', 'error');
    return;
  }

  if (status === 'passed' && !joinDate) {
    showToast('Please enter join date for passed candidates', 'error');
    return;
  }

  // Build FormData for multipart
  const submitData = new FormData();
  submitData.append('action', currentEditId ? 'edit' : 'add');
  submitData.append('candidate_name', candidateName);
  submitData.append('nic', nic);
  submitData.append('phone_number', phone);
  submitData.append('interview_date', interviewDate);
  submitData.append('status', status);
  if (joinDate) {
    submitData.append('join_date', joinDate);
  }
  if (currentEditId) {
    submitData.append('id', currentEditId);
  }
  if (cvFile.files.length > 0) {
    submitData.append('cv_document', cvFile.files[0]);
  }

  // Show loading state
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

  fetch('interview_handler.php', {
    method: 'POST',
    body: submitData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast(data.message, 'success');
      loadInterviews();
      closeAddModal();
    } else {
      showToast(data.message || 'Failed to save interview', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('An error occurred while saving', 'error');
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}

function editInterview(id) {
  // Show loading state
  fetch(`interview_handler.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const interview = data.data;
        currentEditId = id;
        document.getElementById('modalTitle').textContent = 'Edit Interview';
        document.getElementById('candidateName').value = interview.candidate_name;
        document.getElementById('candidateNIC').value = interview.nic;
        document.getElementById('candidatePhone').value = interview.phone_number;
        document.getElementById('interviewDate').value = interview.interview_date;
        document.getElementById('interviewStatus').value = interview.status;
        
        // Make CV optional for edit
        document.getElementById('cvDocument').required = false;
        document.getElementById('cvRequired').style.display = 'none';
        
        // Show existing CV info
        if (interview.cv_path) {
          document.getElementById('existingCvInfo').style.display = 'block';
        } else {
          document.getElementById('existingCvInfo').style.display = 'none';
        }
        
        document.getElementById('cvFileName').textContent = '';
        
        if (interview.status === 'passed' && interview.join_date) {
          document.getElementById('joinDate').value = interview.join_date;
          document.getElementById('joinDateField').classList.remove('hidden-field');
        }

        document.getElementById('interviewModal').classList.add('active');
      } else {
        showToast('Failed to load interview details', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('An error occurred while loading interview', 'error');
    });
}

let deleteTargetId = null;

function deleteInterview(id) {
  deleteTargetId = id;
  document.getElementById('confirmModal').classList.add('active');
}

function closeConfirmModal() {
  document.getElementById('confirmModal').classList.remove('active');
  deleteTargetId = null;
}

function confirmDelete() {
  const submitBtn = document.querySelector('.btn-confirm-delete');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

  fetch('interview_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `action=delete&id=${deleteTargetId}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast(data.message, 'success');
      loadInterviews();
      closeConfirmModal();
    } else {
      showToast(data.message || 'Failed to delete interview', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('An error occurred while deleting', 'error');
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Delete';
  });
}

function renderTable() {
  const tbody = document.getElementById('interviewTableBody');
  
  if (interviews.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 2rem; color: #9ca3af;">
          <i class="fas fa-inbox text-3xl mb-2 block"></i>
          No interviews found. Click "Add Interview" to create one.
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = interviews.map(interview => {
    let statusClass = '';
    if (interview.status === 'pending') statusClass = 'status-pending';
    else if (interview.status === 'passed') statusClass = 'status-passed';
    else if (interview.status === 'rejected') statusClass = 'status-rejected';

    const cvDisplay = interview.cv_path 
      ? `<a href="${interview.cv_path}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Download CV"><i class="fas fa-file-pdf"></i> CV</a>`
      : '<span class="text-gray-400">No file</span>';

    return `
      <tr>
        <td>${interview.candidate_name}</td>
        <td>${interview.nic}</td>
        <td>${interview.phone_number}</td>
        <td>${formatDate(interview.interview_date)}</td>
        <td><span class="status-badge ${statusClass}">${capitalizeFirst(interview.status)}</span></td>
        <td>${cvDisplay}</td>
        <td>
          <button onclick="viewInterview(${interview.id})" class="action-btn btn-view" title="View Details" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"><i class="fas fa-eye"></i> View</button>
          <button onclick="editInterview(${interview.id})" class="action-btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
          <button onclick="deleteInterview(${interview.id})" class="action-btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

function loadInterviews() {
  fetch('interview_handler.php?action=fetch')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        interviews = data.data;
        renderTable();
      } else {
        showToast('Failed to load interviews', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('An error occurred while loading interviews', 'error');
    });
}

function showToast(message, type = 'success') {
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  
  const icon = type === 'success' ? '✓' : '✕';
  toast.innerHTML = `
    <span class="toast-icon">${icon}</span>
    <span class="toast-message">${message}</span>
    <button class="toast-close" onclick="this.parentElement.remove()">
      <i class="fas fa-times"></i>
    </button>
  `;
  
  container.appendChild(toast);
  
  setTimeout(() => {
    toast.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function capitalizeFirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

// Add slideOut animation
const style = document.createElement('style');
style.textContent = `
  @keyframes slideOut {
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);
</script>
