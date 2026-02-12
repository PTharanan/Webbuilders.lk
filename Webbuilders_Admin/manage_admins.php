<?php
require_once 'auth_check.php';
require_once 'config.php';

// Page configuration
$pageTitle = "Admin Management";
$pageSubtitle = "Manage admin records and permissions";

// Page content
ob_start();
?>



<!-- Main Card -->
<div class="bg-white rounded-xl shadow-lg border-t-4 border-[#F36611] overflow-hidden">
    <div class="p-6">
        <!-- Header Row -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users-cog text-gray-600"></i> Admin Records
            </h3>
            <button onclick="openModal('add')" class="bg-[#F36611] hover:bg-[#d9560a] text-white px-6 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Admin
            </button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <div class="md:col-span-9">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput" onkeyup="filterAdmins()" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F36611] focus:border-[#F36611] transition-colors outline-none">
                </div>
            </div>
            <div class="md:col-span-3">
                <select id="statusFilter" onchange="filterAdmins()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F36611] focus:border-[#F36611] outline-none bg-white">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="Left Branch">Left Branch</option>
                    <option value="Relieved">Relieved</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F36611] text-white">
                        <th class="px-6 py-4 font-semibold text-sm">ID</th>
                        <th class="px-6 py-4 font-semibold text-sm">Admin Name</th>
                        <th class="px-6 py-4 font-semibold text-sm">Email</th>
                        <th class="px-6 py-4 font-semibold text-sm">Role</th>
                        <th class="px-6 py-4 font-semibold text-sm">Status</th>
                        <th class="px-6 py-4 font-semibold text-sm">Created Date</th>
                        <th class="px-6 py-4 font-semibold text-sm text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody" class="divide-y divide-gray-100 bg-white">
                    <!-- Data will be loaded via AJAX -->
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading admins...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="adminModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">Add New Admin</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="adminForm" class="space-y-4">
                    <input type="hidden" id="adminId" name="id">
                    <input type="hidden" id="formAction" name="action" value="addAdmin">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Admin Name</label>
                        <input type="text" name="name" id="name" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-[#F36611] focus:border-[#F36611] outline-none shadow-sm">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-[#F36611] focus:border-[#F36611] outline-none shadow-sm">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span id="passwordHint" class="text-xs text-gray-500 font-normal hidden">(Leave blank to keep current)</span></label>
                        <input type="password" name="password" id="password" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-[#F36611] focus:border-[#F36611] outline-none shadow-sm">
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-[#F36611] focus:border-[#F36611] outline-none shadow-sm">
                    </div>

                    <!-- Hidden Auto-fields -->
                    <input type="hidden" name="role" id="role" value="staff">
                    <input type="hidden" name="start_date" id="start_date">
                    
                   
                    <div id="statusField" class="hidden">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-[#F36611] focus:border-[#F36611] outline-none shadow-sm bg-white">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    
                </form>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                <button type="button" onclick="saveAdmin()" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#F36611] text-base font-medium text-white hover:bg-[#d9560a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F36611] sm:text-sm transition-colors">Save Details</button>
                <button type="button" onclick="closeModal()" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F36611] sm:text-sm transition-colors">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();

$additionalCSS = '
.btn-primary {
  background: #F36611;
  transition: all 0.2s ease;
}
.btn-primary:hover {
  background: #d9560a;
}
/* Custom Scrollbar for Table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1; 
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #ccc; 
    border-radius: 4px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #999; 
}
tbody tr:hover {
    background-color: #f9fafb;
}
';

$additionalJS = '
let allAdmins = [];

document.addEventListener("DOMContentLoaded", function() {
    loadAdmins();
});

function openModal(mode, adminId = null) {
    const modal = document.getElementById("adminModal");
    const form = document.getElementById("adminForm");
    const title = document.getElementById("modal-title");
    const passwordHint = document.getElementById("passwordHint");
    const passwordInput = document.getElementById("password");
    
    modal.classList.remove("hidden");
    
    if (mode === "add") {
        title.textContent = "Add New Admin";
        document.getElementById("formAction").value = "addAdmin";
        document.getElementById("adminId").value = "";
        form.reset();
        
        // Hide fields in Add mode (auto-filled)
        // Hide active status field in Add mode if desired, or keep it hidden as per current UI
        const statusField = document.getElementById("statusField");
        if (statusField) statusField.classList.add("hidden");

        // Set default values explicitly
        document.getElementById("role").value = "staff";
        document.getElementById("status").value = "active";
        document.getElementById("start_date").valueAsDate = new Date();
        
        passwordInput.placeholder = "Enter password";
        passwordHint.classList.add("hidden");
    } else {
        title.textContent = "Edit Admin Details";
        document.getElementById("formAction").value = "updateAdmin";
        document.getElementById("adminId").value = adminId;
        
        // Show fields in Edit mode
        // Show status field in Edit mode if it exists
        const statusField = document.getElementById("statusField");
        if (statusField) statusField.classList.remove("hidden");
        
        passwordInput.placeholder = "Enter new password to change";
        passwordHint.classList.remove("hidden");
        
        // Find admin from local data instead of fetching again (since we have it)
        const admin = allAdmins.find(a => a.id == adminId);
        if (admin) {
            document.getElementById("name").value = admin.name;
            document.getElementById("email").value = admin.email;
            
            // Populate status if it exists and matches lowercase values
            const statusEl = document.getElementById("status");
            if (statusEl) statusEl.value = admin.status.toLowerCase();
            
            const roleEl = document.getElementById("role");
            if (roleEl) roleEl.value = admin.role.toLowerCase();
            
            if (admin.created_at) {
                 const date = new Date(admin.created_at);
                 const dateEl = document.getElementById("start_date");
                 if (dateEl) dateEl.value = date.toISOString().split("T")[0];
            }
        }
    }
}

function closeModal() {
    document.getElementById("adminModal").classList.add("hidden");
}

function loadAdmins() {
    fetch("manageAdmin.php?action=getAllAdmins")
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("adminTableBody");
            
            if (data.success) {
                allAdmins = data.data; // Store for filtering
                renderTable(allAdmins);
            } else {
                tbody.innerHTML = "<tr><td colspan=\'7\' class=\'px-6 py-8 text-center text-red-500\'>Error: " + data.message + "</td></tr>";
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById("adminTableBody").innerHTML = "<tr><td colspan=\'7\' class=\'px-6 py-8 text-center text-red-500\'>Error loading data.</td></tr>";
        });
}

function renderTable(admins) {
    const tbody = document.getElementById("adminTableBody");
    tbody.innerHTML = "";
    
    if (admins.length === 0) {
        tbody.innerHTML = "<tr><td colspan=\'7\' class=\'px-6 py-8 text-center text-gray-500\'>No records found.</td></tr>";
        return;
    }
    
    admins.forEach(admin => {
        const tr = document.createElement("tr");
        tr.className = "border-b border-gray-100 transition-colors";
        
        let statusBadge = "";
        if (admin.status === "active") {
            statusBadge = "<span class=\'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800\'>Working</span>";
        } else if (admin.status === "inactive") {
            statusBadge = "<span class=\'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800\'>inactive</span>";
        } else {
             statusBadge = `<span class=\'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800\'>${admin.status}</span>`;
        }
        
        const date = new Date(admin.created_at).toISOString().split("T")[0]; // Just YYYY-MM-DD
        
        tr.innerHTML = `
            <td class="px-6 py-4 text-sm text-gray-500">${admin.id}</td>
            <td class="px-6 py-4 font-medium text-gray-900">${admin.name}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${admin.email}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${admin.role}</td>
            <td class="px-6 py-4">${statusBadge}</td>
            <td class="px-6 py-4 text-sm text-gray-600 font-mono">${date}</td>
            <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                    <button onclick="openModal(\'edit\', ${admin.id})" class="bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-md flex items-center justify-center transition-colors shadow-sm" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button onclick="deleteAdmin(${admin.id})" class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-md flex items-center justify-center transition-colors shadow-sm" title="Delete">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function filterAdmins() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();
    const statusFilter = document.getElementById("statusFilter").value;
    
    const filtered = allAdmins.filter(admin => {
        const matchesSearch = admin.name.toLowerCase().includes(searchTerm) || 
                              emailMatch(admin.email, searchTerm);
        const matchesStatus = statusFilter === "" || admin.status === statusFilter;
        return matchesSearch && matchesStatus;
    });
    
    renderTable(filtered);
}

function emailMatch(email, term) {
    return email && email.toLowerCase().includes(term);
}

function saveAdmin() {
    const form = document.getElementById("adminForm");
    const formData = new FormData(form);
    
    if (!formData.get("name") || !formData.get("email")) {
        alert("Please fill in all required fields.");
        return;
    }
    
    fetch("manageAdmin.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            loadAdmins();
            // Optional: Show toast
        } else {
            alert(data.message || "Operation failed.");
        }
    })
    .catch(err => alert("Error saving data."));
}

function deleteAdmin(id) {
    if (!confirm("Are you sure you want to delete this admin permanently?")) return;
    
    const formData = new FormData();
    formData.append("action", "deleteAdmin");
    formData.append("id", id);
    
    fetch("manageAdmin.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadAdmins();
        } else {
            alert(data.message || "Delete failed.");
        }
    });
}
</script>
';

include 'layout.php';
?>
