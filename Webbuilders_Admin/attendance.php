<?php
require_once 'auth_check.php';
require_once 'config.php';
require_once 'dbConnect.php';

$pageTitle = 'Attendance Management';
$pageSubtitle = 'Track and manage employee attendance';

ob_start();

// Get current month and year from URL parameters
$currentYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Validate and wrap month/year
if ($currentMonth < 1) {
    $currentMonth = 12;
    $currentYear--;
} elseif ($currentMonth > 12) {
    $currentMonth = 1;
    $currentYear++;
}

// Get days in current month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

// Get first and last day of the month
$monthStartDate = sprintf("%04d-%02d-01", $currentYear, $currentMonth);
$monthEndDate = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $daysInMonth);

// Fetch employees who:
// 1. Joined on or before the end of the month (joining_date <= month_end)
// 2. AND (left_date is NULL OR left_date > month_start)
// This includes current employees and those who left after the month started
$stmt = $conn->prepare("
    SELECT DISTINCT e.id, e.name 
    FROM employees e
    WHERE e.joining_date <= :month_end 
    AND (e.left_date IS NULL OR e.left_date > :month_start)
    ORDER BY e.name ASC
");
$stmt->execute([
    ':month_start' => $monthStartDate,
    ':month_end' => $monthEndDate
]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to fetch attendance record
function getAttendanceRecord($conn, $employeeId, $year, $month, $day) {
    $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id = :employee_id AND attendance_date = :attendance_date");
    $stmt->execute([
        ':employee_id' => $employeeId,
        ':attendance_date' => $dateStr
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to calculate total working days (excluding Sundays) until current day
function getTotalWorkingDaysUntilToday($year, $month) {
    $today = new DateTime();
    $currentYear = (int)$today->format('Y');
    $currentMonth = (int)$today->format('m');
    $currentDay = (int)$today->format('d');
    
    $workingDays = 0;
    
    // If viewing a past month, count all working days
    if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $dayOfWeek = date('w', strtotime($dateStr));
            if ($dayOfWeek != 0) { // 0 = Sunday
                $workingDays++;
            }
        }
    } else if ($year == $currentYear && $month == $currentMonth) {
        // Current month: count up to today
        for ($day = 1; $day <= $currentDay; $day++) {
            $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $dayOfWeek = date('w', strtotime($dateStr));
            if ($dayOfWeek != 0) { // 0 = Sunday
                $workingDays++;
            }
        }
    }
    
    return $workingDays;
}

// Function to get employee attendance stats for the current month
function getEmployeeStats($conn, $employeeId, $year, $month) {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $attendanceDays = 0;
    $tasklogDays = 0;
    
    // Count only up to today if viewing current month
    $today = new DateTime();
    $currentYear = (int)$today->format('Y');
    $currentMonth = (int)$today->format('m');
    $currentDay = (int)$today->format('d');
    
    $maxDay = $daysInMonth;
    if ($year == $currentYear && $month == $currentMonth) {
        $maxDay = $currentDay;
    }
    
    for ($day = 1; $day <= $maxDay; $day++) {
        $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $dayOfWeek = date('w', strtotime($dateStr));
        
        // Skip Sundays
        if ($dayOfWeek == 0) continue;
        
        $record = getAttendanceRecord($conn, $employeeId, $year, $month, $day);
        if ($record) {
            if ($record['attendance']) {
                $attendanceDays++;
            }
            if ($record['tasklog_submitted']) {
                $tasklogDays++;
            }
        }
    }
    
    return ['attendance' => $attendanceDays, 'tasklog' => $tasklogDays];
}

// Calculate navigation values
$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
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
  
  .btn-primary {
    background-color: #f97316;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
  }
  
  .btn-primary:hover {
    background-color: #ea580c;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);
  }
  
  .btn-secondary {
    background-color: #e5e7eb;
    color: #374151;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
  }
  
  .dark .btn-secondary {
    background-color: #334155;
    color: #e5e7eb;
  }
  
  .btn-secondary:hover {
    background-color: #d1d5db;
  }
  
  .dark .btn-secondary:hover {
    background-color: #475569;
  }

  .table-container {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-top: 1.5rem;
    background: white;
  }

  .dark .table-container {
    background: #1e293b;
  }
  
  .attendance-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 2px;
    background: white;
    font-size: 0.875rem;
  }
  
  .dark .attendance-table {
    background: #1e293b;
  }
  
  .attendance-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
  }

  .attendance-table thead tr:first-child {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  }

  .attendance-table thead tr:nth-child(2) {
    background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
  }
  
  .attendance-table th {
    padding: 0.875rem 0.75rem;
    text-align: center;
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.8rem;
    color: white;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
  }

  .attendance-table th:last-child {
    border-right: none;
  }

  .employee-header {
    text-align: left !important;
    min-width: 200px;
    max-width: 200px;
    position: sticky;
    left: 0;
    z-index: 15;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  }

  .employee-subheader {
    position: sticky;
    left: 0;
    z-index: 15;
    background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  }
  
  .attendance-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
    border-right: 1px solid #f3f4f6;
    text-align: center;
    transition: all 0.2s ease;
  }

  .attendance-table td:last-child {
    border-right: none;
  }
  
  .dark .attendance-table td {
    border-bottom-color: #334155;
    border-right-color: #334155;
  }
  
  .attendance-table tbody tr {
    transition: all 0.3s ease;
  }
  
  .attendance-table tbody tr:hover {
    background: #fff7ed;
    transform: scale(1.001);
  }
  
  .dark .attendance-table tbody tr:hover {
    background: #0f172a;
  }

  .employee-name {
    text-align: left !important;
    font-weight: 600;
    color: #1f2937;
    padding: 1rem !important;
    min-width: 200px;
    max-width: 200px;
    position: sticky;
    left: 0;
    background: white;
    z-index: 5;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
  }

  .dark .employee-name {
    color: #e5e7eb;
    background: #1e293b;
  }

  .attendance-table tbody tr:hover .employee-name {
    background: #fff7ed;
  }

  .dark .attendance-table tbody tr:hover .employee-name {
    background: #0f172a;
  }

  .employee-name i {
    margin-right: 0.5rem;
    color: #f97316;
    font-size: 1.1rem;
  }

  .status-header {
    text-align: center !important;
    min-width: 140px;
    max-width: 140px;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  }

  .status-cell {
    padding: 1rem 0.75rem !important;
    min-width: 140px;
    max-width: 140px;
    text-align: center;
    background: #fafafa;
    border-right: 2px solid #e5e7eb;
  }

  .dark .status-cell {
    background: #0f172a;
    border-right-color: #334155;
  }

  .status-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .status-stat {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    font-size: 0.85rem;
    font-weight: 600;
  }

  .status-attendance {
    color: #059669;
  }

  .status-tasklog {
    color: #2563eb;
  }

  .status-icon {
    font-size: 0.9rem;
  }

  .time-input {
    width: 70px;
    padding: 0.4rem 0.3rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.75rem;
    background: #f9fafb;
    color: #1f2937;
    text-align: center;
    transition: all 0.2s ease;
    font-weight: 500;
  }

  .dark .time-input {
    background: #0f172a;
    border-color: #334155;
    color: #e5e7eb;
  }

  .time-input:focus {
    outline: none;
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    background: white;
  }

  .dark .time-input:focus {
    background: #1e293b;
  }

  .day-header {
    font-size: 0.85rem;
    padding: 0.875rem 0.5rem !important;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  .time-header {
    font-size: 0.7rem;
    padding: 0.625rem 0.35rem !important;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    opacity: 0.95;
  }

  .time-input-group {
    display: none !important;
    visibility: hidden;
    height: 0;
    padding: 0;
    margin: 0;
    opacity: 0;
    transition: all 0.2s ease;
  }

  .time-input-group.visible {
    display: block !important;
    visibility: visible;
    height: auto;
    padding: 0;
    margin: 0;
    opacity: 1;
  }

  .attendance-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #f97316;
    display: block;
    margin: 0 auto;
    transition: transform 0.2s ease;
  }

  .attendance-checkbox:hover {
    transform: scale(1.15);
  }

  .attendance-checkbox:checked {
    transform: scale(1.1);
  }

  .tasklog-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #3b82f6;
    display: block;
    margin: 0 auto;
    transition: transform 0.2s ease;
  }

  .tasklog-checkbox:hover {
    transform: scale(1.15);
  }

  .tasklog-checkbox:checked {
    transform: scale(1.1);
  }

  .day-column {
    min-width: 150px;
    padding: 0.5rem !important;
  }

  .saturday-col {
    background-color: transparent !important;
  }

  .dark .saturday-col {
    background-color: transparent !important;
  }

  .saturday-col th {
    background: linear-gradient(135deg, #fb923c 0%, #f97316 100%) !important;
    color: white !important;
  }

  .sunday-col {
    background-color: #fee2e2 !important;
  }

  .sunday-col th {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    color: white !important;
  }

  .dark .sunday-col {
    background-color: rgba(127, 29, 29, 0.3) !important;
  }

  .attendance-table tbody tr:nth-child(even) {
    background-color: #fafafa;
  }

  .dark .attendance-table tbody tr:nth-child(even) {
    background-color: #0f172a;
  }

  .day-update-btn {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    border: none;
    border-radius: 8px;
    padding: 0.35rem 0.6rem !important;
    font-size: 0.75rem !important;
    color: white !important;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 32px;
  }

  .day-update-btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.5s ease;
  }

  .day-update-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
  }

  .day-update-btn:hover:before {
    left: 100%;
  }

  .day-update-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
  }

  .day-update-btn i {
    position: relative;
    z-index: 1;
  }

  .day-update-btn.btn-edit {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  }

  .day-update-btn.btn-edit:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  .day-update-btn.btn-edit:hover:before {
    content: "Edit";
    position: absolute;
    bottom: -25px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(30, 64, 175, 0.95);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    z-index: 10;
  }

  .time-display {
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .time-display:hover {
    background-color: #e0f2fe;
    color: #0284c7;
    border-radius: 4px;
    transform: scale(1.05);
  }

  .time-input-group:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .time-input-group:enabled {
    opacity: 1;
    cursor: text;
    background-color: #fef3c7 !important;
    border: 2px solid #f59e0b !important;
  }

  .month-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .dark .month-navigation {
    background: #1e293b;
  }

  .nav-button {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
  }

  .nav-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
  }

  .nav-button:active {
    transform: translateY(0);
  }

  .nav-button i {
    font-size: 1.1rem;
  }

  .month-display {
    text-align: center;
    flex: 1;
  }

  .month-display h2 {
    margin: 0;
    font-size: 1.8rem;
    color: #1f2937;
    font-weight: 700;
  }

  .dark .month-display h2 {
    color: #e5e7eb;
  }

  .month-display p {
    margin: 0.25rem 0 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }

  .alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    display: none;
  }

  .alert.show {
    display: block;
  }

  .alert-success {
    background-color: #ecfdf5;
    color: #065f46;
    border: 1px solid #6ee7b7;
  }

  .dark .alert-success {
    background-color: rgba(16, 185, 129, 0.2);
    border-color: #10b981;
  }

  .alert-error {
    background-color: #fef2f2;
    color: #7f1d1d;
    border: 1px solid #fca5a5;
  }

  .dark .alert-error {
    background-color: rgba(239, 68, 68, 0.2);
    border-color: #ef4444;
  }

  /* Toast Notifications */
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
    padding: 1rem 1.5rem;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.3s ease-out;
    pointer-events: auto;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .toast.hide {
    animation: slideOut 0.3s ease-out forwards;
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

  .toast-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
  }

  .toast-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .toast-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
  }

  .toast-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .toast-message {
    font-size: 0.95rem;
    font-weight: 500;
    flex: 1;
  }

  .toast-close {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 1.25rem;
    padding: 0;
    opacity: 0.8;
    transition: opacity 0.2s;
  }

  .toast-close:hover {
    opacity: 1;
  }
</style>

<div id="toastContainer" class="toast-container"></div>

<!-- Month Navigation -->
<div class="month-navigation" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
  <button class="nav-button" onclick="navigateMonth(<?php echo $prevYear; ?>, <?php echo $prevMonth; ?>)">
    <i class="fas fa-chevron-left"></i> Previous
  </button>
  <div class="month-display">
    <h2><?php echo date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); ?></h2>
    <p>
      <?php echo $daysInMonth; ?> days in this month • 
      <strong><?php echo getTotalWorkingDaysUntilToday($currentYear, $currentMonth); ?> working days</strong> until today
    </p>
  </div>
  <div style="display: flex; gap: 0.5rem;">
    <button class="nav-button" style="background: #10b981; border-color: #10b981; color: white;" onclick="exportToExcel()">
      <i class="fas fa-file-excel"></i> Export Excel
    </button>
    <button class="nav-button" style="background: #ef4444; border-color: #ef4444; color: white;" onclick="exportToPDF()">
      <i class="fas fa-file-pdf"></i> Export PDF
    </button>
  </div>
  <button class="nav-button" onclick="navigateMonth(<?php echo $nextYear; ?>, <?php echo $nextMonth; ?>)">
    Next <i class="fas fa-chevron-right"></i>
  </button>
</div>

<!-- Attendance Table -->
<div class="table-container">
  <table class="attendance-table">
    <thead>
      <tr>
        <th class="employee-header" style="text-align: left !important; padding-left: 1rem;">
          Employee Name<br>
          <span style="font-size: 0.65rem; font-weight: 500; opacity: 0.9;"><?php echo date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); ?></span>
        </th>
        <th class="status-header">
          Status<br>
          <span style="font-size: 0.65rem; font-weight: 500; opacity: 0.9;">This Month</span>
        </th>
        <?php for ($day = 1; $day <= $daysInMonth; $day++): 
          $dateStr = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $day);
          $dayOfWeek = date('w', strtotime($dateStr));
          $dayName = date('D', strtotime($dateStr)); // Get day name (Mon, Tue, etc.)
          $isSaturday = ($dayOfWeek == 6);
          $isSunday = ($dayOfWeek == 0);
          $colClass = $isSunday ? 'sunday-col' : '';
        ?>
          <th colspan="6" class="day-column <?php echo $colClass; ?>">
            <?php echo $day; ?><br>
            <span style="font-size: 0.65rem; font-weight: 500; opacity: 0.9;"><?php echo $dayName; ?></span>
          </th>
        <?php endfor; ?>
      </tr>
      <tr>
        <th class="employee-subheader"></th>
        <th class="status-header" style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);"></th>
        <?php for ($day = 1; $day <= $daysInMonth; $day++): 
          $dateStr = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $day);
          $dayOfWeek = date('w', strtotime($dateStr));
          $isSaturday = ($dayOfWeek == 6);
          $isSunday = ($dayOfWeek == 0);
          $colClass = $isSunday ? 'sunday-col' : '';
        ?>
          <?php if (!$isSunday): ?>
            <th class="time-header <?php echo $colClass; ?>">✓</th>
            <th class="time-header <?php echo $colClass; ?>" title="Tasklog">Task</th>
            <th class="time-header <?php echo $colClass; ?>">In</th>
            <th class="time-header <?php echo $colClass; ?>">Out</th>
            <th class="time-header <?php echo $colClass; ?>" style="font-size: 0.55rem;">Hours</th>
            <th class="time-header <?php echo $colClass; ?>" style="font-size: 0.6rem;">Save</th>
          <?php else: ?>
            <th class="time-header sunday-col" colspan="6">Holiday</th>
          <?php endif; ?>
        <?php endfor; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($employees)): ?>
        <tr>
          <td colspan="<?php echo ($daysInMonth * 2); ?>" style="text-align: center; padding: 2rem; color: #9ca3af;">
            <i class="fas fa-inbox text-3xl mb-2 block"></i>
            No working employees found.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($employees as $employee): ?>
          <?php $stats = getEmployeeStats($conn, $employee['id'], $currentYear, $currentMonth); ?>
          <tr>
            <td class="employee-name">
              <i class="fas fa-user-circle" style="margin-right: 0.5rem;"></i><?php echo htmlspecialchars($employee['name']); ?>
            </td>
            <td class="status-cell">
              <div class="status-content">
                <div class="status-stat status-attendance">
                  <i class="fas fa-check-circle status-icon"></i>
                  <span><?php echo $stats['attendance']; ?> days</span>
                </div>
                <div class="status-stat status-tasklog">
                  <i class="fas fa-tasks status-icon"></i>
                  <span><?php echo $stats['tasklog']; ?> days</span>
                </div>
              </div>
            </td>
            <?php for ($day = 1; $day <= $daysInMonth; $day++): 
              $dateStr = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $day);
              $dayOfWeek = date('w', strtotime($dateStr));
              $isSaturday = ($dayOfWeek == 6);
              $isSunday = ($dayOfWeek == 0);
              $colClass = $isSunday ? 'sunday-col' : '';
              $sundayClass = $isSunday ? 'sunday-col' : '';
              
              // Fetch attendance record for this employee and day
              $record = getAttendanceRecord($conn, $employee['id'], $currentYear, $currentMonth, $day);
            ?>
              <?php if (!$isSunday): ?>
                <td class="attendance-cell <?php echo $colClass; ?>">
                  <input type="checkbox" class="attendance-checkbox" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" title="Mark attendance for day <?php echo $day; ?>" <?php echo ($record && $record['attendance']) ? 'checked' : ''; ?>>
                </td>
                <td class="attendance-cell <?php echo $colClass; ?>">
                  <input type="checkbox" class="tasklog-checkbox" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" title="Task log submitted for day <?php echo $day; ?>" <?php echo ($record && $record['tasklog_submitted']) ? 'checked' : ''; ?>>
                </td>
                <td class="attendance-cell <?php echo $colClass; ?>" style="position: relative;">
                  <?php if ($record && $record['in_time']): ?>
                    <?php 
                      $inTime = DateTime::createFromFormat('H:i:s', $record['in_time']) ?: DateTime::createFromFormat('H:i', $record['in_time']);
                      $formattedInTime = $inTime ? $inTime->format('g:i A') : $record['in_time'];
                    ?>
                    <span class="time-display in-time-display" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" data-type="in" style="font-size: 0.8rem; font-weight: 600; color: #059669; display: block; text-align: center; margin-bottom: 0.1rem; cursor: pointer; padding: 0.2rem; border-radius: 3px; transition: all 0.2s ease;" title="Click to edit in time"><?php echo $formattedInTime; ?></span>
                  <?php endif; ?>
                  <input type="time" class="time-input in-time time-input-group" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" data-type="in" title="In time for day <?php echo $day; ?>" value="<?php echo ($record && $record['in_time']) ? substr($record['in_time'], 0, 5) : ''; ?>" style="<?php echo ($record && $record['in_time']) ? 'background-color: #d1fae5; border: 1px solid #6ee7b7;' : ''; ?>">
                </td>
                <td class="attendance-cell <?php echo $colClass; ?>" style="position: relative;">
                  <?php if ($record && $record['out_time']): ?>
                    <?php 
                      $outTime = DateTime::createFromFormat('H:i:s', $record['out_time']) ?: DateTime::createFromFormat('H:i', $record['out_time']);
                      $formattedOutTime = $outTime ? $outTime->format('g:i A') : $record['out_time'];
                    ?>
                    <span class="time-display out-time-display" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" data-type="out" style="font-size: 0.8rem; font-weight: 600; color: #059669; display: block; text-align: center; margin-bottom: 0.1rem; cursor: pointer; padding: 0.2rem; border-radius: 3px; transition: all 0.2s ease;" title="Click to edit out time"><?php echo $formattedOutTime; ?></span>
                  <?php endif; ?>
                  <input type="time" class="time-input out-time time-input-group" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" data-type="out" title="Out time for day <?php echo $day; ?>" value="<?php echo ($record && $record['out_time']) ? substr($record['out_time'], 0, 5) : ''; ?>" style="<?php echo ($record && $record['out_time']) ? 'background-color: #d1fae5; border: 1px solid #6ee7b7;' : ''; ?>">
                </td>
                <td class="attendance-cell <?php echo $colClass; ?>" style="font-size: 0.8rem; font-weight: 600; color: <?php echo ($record && $record['working_hours'] && $record['in_time'] && $record['out_time']) ? '#10b981' : '#64748b'; ?>;">
                  <span class="working-hours" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>">
                    <?php 
                    if ($record && $record['working_hours']) {
                      $hours = floor($record['working_hours']);
                      $mins = round(($record['working_hours'] - $hours) * 60);
                      echo "{$hours}h {$mins}m";
                    } else {
                      echo '—';
                    }
                    ?>
                  </span>
                </td>
                <td class="attendance-cell <?php echo $colClass; ?>" style="padding: 0.25rem !important;">
                  <button class="day-update-btn <?php echo ($record) ? 'btn-edit' : 'btn-add'; ?>" data-employee-id="<?php echo $employee['id']; ?>" data-day="<?php echo $day; ?>" data-has-record="<?php echo ($record) ? 'true' : 'false'; ?>" title="<?php echo ($record) ? 'Edit attendance for day ' . $day : 'Save attendance for day ' . $day; ?>">
                    <i class="fas fa-<?php echo ($record) ? 'edit' : 'check'; ?>"></i>
                  </button>
                </td>
              <?php else: ?>
                <td class="attendance-cell <?php echo $sundayClass; ?>" colspan="6">
                  <span style="color: #64748b; font-size: 0.75rem; font-weight: 600;">—</span>
                </td>
              <?php endif; ?>
            <?php endfor; ?>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Edit Attendance Modal -->
<div id="editAttendanceModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 8px; padding: 2rem; max-width: 500px; width: 90%; box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 style="margin: 0; font-size: 1.5rem; color: #1f2937;">Edit Attendance</h2>
      <button id="closeModalBtn" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">×</button>
    </div>
    
    <div style="margin-bottom: 1rem;">
      <p style="margin: 0.5rem 0; color: #4b5563;"><strong>Employee:</strong> <span id="modalEmployeeName"></span></p>
      <p style="margin: 0.5rem 0; color: #4b5563;"><strong>Date:</strong> <span id="modalDate"></span></p>
    </div>

    <form id="editAttendanceForm" style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" id="modalAttendance" style="width: 18px; height: 18px; cursor: pointer;">
          <span>Attendance</span>
        </label>
      </div>

      <div>
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" id="modalTasklog" style="width: 18px; height: 18px; cursor: pointer;">
          <span>Task Log Submitted</span>
        </label>
      </div>

      <div>
        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">In Time</label>
        <input type="time" id="modalInTime" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1rem;">
      </div>

      <div>
        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Out Time</label>
        <input type="time" id="modalOutTime" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1rem;">
      </div>

      <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="button" id="modalCancelBtn" style="flex: 1; padding: 0.75rem; background-color: #e5e7eb; color: #374151; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Cancel</button>
        <button type="submit" style="flex: 1; padding: 0.75rem; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Global variables for current month and year
  const CURRENT_MONTH = <?php echo $currentMonth; ?>;
  const CURRENT_YEAR = <?php echo $currentYear; ?>;

  // Show toast notification
  function showAlert(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    
    // Icon mapping
    const icons = {
      success: 'fas fa-check-circle',
      error: 'fas fa-exclamation-circle',
      info: 'fas fa-info-circle'
    };

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      <i class="toast-icon ${icons[type] || icons.info}"></i>
      <span class="toast-message">${message}</span>
      <button class="toast-close">&times;</button>
    `;

    // Add close button functionality
    toast.querySelector('.toast-close').addEventListener('click', () => {
      toast.classList.add('hide');
      setTimeout(() => toast.remove(), 300);
    });

    // Add to container
    container.appendChild(toast);

    // Auto remove after 4 seconds
    setTimeout(() => {
      if (toast.parentElement) {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
      }
    }, 4000);
  }

  // Handle attendance checkbox and time inputs
  document.querySelectorAll('.attendance-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const employeeId = this.getAttribute('data-employee-id');
      const day = this.getAttribute('data-day');
      const isChecked = this.checked;
      
      // Find the corresponding in and out time inputs
      const inTimeInput = document.querySelector(`.in-time[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const outTimeInput = document.querySelector(`.out-time[data-employee-id="${employeeId}"][data-day="${day}"]`);

      
      if (isChecked) {
        // Show time inputs
        inTimeInput.classList.add('visible');
        outTimeInput.classList.add('visible');
        inTimeInput.focus();
      } else {
        // Hide time inputs and clear values
        inTimeInput.classList.remove('visible');
        outTimeInput.classList.remove('visible');
        inTimeInput.value = '';
        outTimeInput.value = '';
      }
      
      console.log(`Employee ${employeeId} - Day ${day}: ${isChecked ? 'Present' : 'Absent'}`);
    });
  });

  // Handle tasklog checkbox
  document.querySelectorAll('.tasklog-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const employeeId = this.getAttribute('data-employee-id');
      const day = this.getAttribute('data-day');
      const isChecked = this.checked;
      
      console.log(`Employee ${employeeId} - Day ${day} - Tasklog: ${isChecked ? 'Submitted' : 'Not Submitted'}`);
      // Add your API call here to save tasklog status
    });
  });

  // Handle day-specific update buttons
  document.querySelectorAll('.day-update-btn').forEach(button => {
    button.addEventListener('click', function() {
      const employeeId = this.getAttribute('data-employee-id');
      const day = this.getAttribute('data-day');
      const hasRecord = this.getAttribute('data-has-record') === 'true';
      const currentButton = this;

      // Get data for this specific day
      const attendanceCheckbox = document.querySelector(`.attendance-checkbox[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const tasklogCheckbox = document.querySelector(`.tasklog-checkbox[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const inTimeInput = document.querySelector(`.in-time[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const outTimeInput = document.querySelector(`.out-time[data-employee-id="${employeeId}"][data-day="${day}"]`);

      console.log('Button clicked for Employee:', employeeId, 'Day:', day);
      console.log('Found time inputs:', {inTimeValue: inTimeInput?.value, outTimeValue: outTimeInput?.value});

      // If it's an edit button and record exists, open modal for editing
      if (hasRecord && this.classList.contains('btn-edit')) {
        console.log('Opening edit modal with times:', {in: inTimeInput?.value, out: outTimeInput?.value});
        openEditModal(employeeId, day, attendanceCheckbox.checked, tasklogCheckbox.checked, inTimeInput?.value, outTimeInput?.value);
        return;
      }

      // Validate: Attendance checkbox must be checked
      if (!attendanceCheckbox.checked) {
        showAlert(`Please mark attendance for day ${day}`, 'error');
        return;
      }

      // Validate times if both are provided
      if (inTimeInput.value && outTimeInput.value) {
        const inTime = new Date(`2000-01-01 ${inTimeInput.value}`);
        const outTime = new Date(`2000-01-01 ${outTimeInput.value}`);
        if (outTime <= inTime) {
          showAlert(`Invalid times for day ${day}: Out time must be after in time`, 'error');
          return;
        }
      }

      const attendanceData = [{
        employee_id: employeeId,
        records: [{
          day: day,
          attendance: attendanceCheckbox.checked,
          in_time: inTimeInput.value || null,
          out_time: outTimeInput.value || null,
          tasklog_submitted: tasklogCheckbox.checked
        }],
        month: CURRENT_MONTH,
        year: CURRENT_YEAR
      }];

      console.log('📤 Sending attendance data:', attendanceData);
      console.log('📝 Employee ID:', employeeId, 'Day:', day);

      // Send to server
      fetch('attendance_handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(attendanceData)
      })
      .then(response => {
        console.log('📥 Response status:', response.status, response.statusText);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('✅ Response data:', data);
        if (data.success) {
          // Show success feedback on button
          const originalGradient = currentButton.style.background;
          currentButton.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
          showAlert(`Day ${day} updated successfully`, 'success');
          
          // Keep attendance checkbox checked
          attendanceCheckbox.checked = true;
          
          // Update time displays with formatted times
          if (inTimeInput.value) {
            let inTimeDisplay = document.querySelector(`.in-time-display[data-employee-id="${employeeId}"][data-day="${day}"]`);
            
            if (!inTimeDisplay) {
              // Create new time display span if it doesn't exist
              inTimeDisplay = document.createElement('span');
              inTimeDisplay.className = 'time-display in-time-display';
              inTimeDisplay.setAttribute('data-employee-id', employeeId);
              inTimeDisplay.setAttribute('data-day', day);
              inTimeDisplay.setAttribute('data-type', 'in');
              inTimeDisplay.style.cssText = 'font-size: 0.8rem; font-weight: 600; color: #059669; display: block; text-align: center; margin-bottom: 0.1rem; cursor: pointer; padding: 0.2rem; border-radius: 3px; transition: all 0.2s ease;';
              inTimeDisplay.title = 'Click to edit in time';
              
              const inTimeCell = document.querySelector(`.in-time[data-employee-id="${employeeId}"][data-day="${day}"]`).parentElement;
              inTimeCell.insertBefore(inTimeDisplay, inTimeCell.firstChild);
            }
            
            // Format and display the time
            const inTimeObj = new Date(`2000-01-01 ${inTimeInput.value}`);
            inTimeDisplay.textContent = inTimeObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
          }
          
          if (outTimeInput.value) {
            let outTimeDisplay = document.querySelector(`.out-time-display[data-employee-id="${employeeId}"][data-day="${day}"]`);
            
            if (!outTimeDisplay) {
              // Create new time display span if it doesn't exist
              outTimeDisplay = document.createElement('span');
              outTimeDisplay.className = 'time-display out-time-display';
              outTimeDisplay.setAttribute('data-employee-id', employeeId);
              outTimeDisplay.setAttribute('data-day', day);
              outTimeDisplay.setAttribute('data-type', 'out');
              outTimeDisplay.style.cssText = 'font-size: 0.8rem; font-weight: 600; color: #059669; display: block; text-align: center; margin-bottom: 0.1rem; cursor: pointer; padding: 0.2rem; border-radius: 3px; transition: all 0.2s ease;';
              outTimeDisplay.title = 'Click to edit out time';
              
              const outTimeCell = document.querySelector(`.out-time[data-employee-id="${employeeId}"][data-day="${day}"]`).parentElement;
              outTimeCell.insertBefore(outTimeDisplay, outTimeCell.firstChild);
            }
            
            // Format and display the time
            const outTimeObj = new Date(`2000-01-01 ${outTimeInput.value}`);
            outTimeDisplay.textContent = outTimeObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
          }
          
          // Update working hours
          const workingHoursSpan = document.querySelector(`.working-hours[data-employee-id="${employeeId}"][data-day="${day}"]`);
          if (inTimeInput.value && outTimeInput.value && workingHoursSpan) {
            const inTimeObj = new Date(`2000-01-01 ${inTimeInput.value}`);
            const outTimeObj = new Date(`2000-01-01 ${outTimeInput.value}`);
            if (outTimeObj > inTimeObj) {
              const diffMs = outTimeObj - inTimeObj;
              const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
              const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
              workingHoursSpan.textContent = `${diffHours}h ${diffMins}m`;
              workingHoursSpan.style.color = '#10b981';
            }
          }
          
          // After success, change to edit button
          setTimeout(() => {
            currentButton.style.background = '';
            currentButton.classList.remove('btn-add');
            currentButton.classList.add('btn-edit');
            currentButton.querySelector('i').className = 'fas fa-edit';
            currentButton.setAttribute('data-has-record', 'true');
            currentButton.title = `Edit attendance for day ${day}`;
            
            // Hide the inline time inputs
            inTimeInput.classList.remove('visible');
            outTimeInput.classList.remove('visible');
          }, 1500);
        } else {
          const failedMsg = data.failed_records ? data.failed_records.join(', ') : data.message;
          console.error('❌ Server error:', failedMsg);
          showAlert(`Error: ${failedMsg || 'Failed to update attendance'}`, 'error');

        }
      })
      .catch(error => {
        console.error('🔴 Fetch error:', error);
        console.error('Error message:', error.message);
        showAlert('Error updating attendance: ' + error.message, 'error');
      });
    });
  });

  // Add click listeners to time displays to enable editing
  document.querySelectorAll('.time-display').forEach(timeDisplay => {
    timeDisplay.addEventListener('click', function(e) {
      e.stopPropagation();
      const employeeId = this.getAttribute('data-employee-id');
      const day = this.getAttribute('data-day');

      // Get data for this specific day to open modal
      const attendanceCheckbox = document.querySelector(`.attendance-checkbox[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const tasklogCheckbox = document.querySelector(`.tasklog-checkbox[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const inTimeInput = document.querySelector(`.in-time[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const outTimeInput = document.querySelector(`.out-time[data-employee-id="${employeeId}"][data-day="${day}"]`);
      
      // Open the modal for editing
      openEditModal(employeeId, day, attendanceCheckbox.checked, tasklogCheckbox.checked, inTimeInput?.value, outTimeInput?.value);
    });
  });

  // Handle time input change
  document.querySelectorAll('.time-input').forEach(input => {
    input.addEventListener('change', function() {
      const employeeId = this.getAttribute('data-employee-id');
      const day = this.getAttribute('data-day');
      const type = this.getAttribute('data-type');
      const time = this.value;
      
      // Calculate working hours
      const inTimeInput = document.querySelector(`.in-time[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const outTimeInput = document.querySelector(`.out-time[data-employee-id="${employeeId}"][data-day="${day}"]`);
      const workingHoursSpan = document.querySelector(`.working-hours[data-employee-id="${employeeId}"][data-day="${day}"]`);
      
      if (inTimeInput.value && outTimeInput.value) {
        const inTime = new Date(`2000-01-01 ${inTimeInput.value}`);
        const outTime = new Date(`2000-01-01 ${outTimeInput.value}`);
        
        if (outTime > inTime) {
          const diffMs = outTime - inTime;
          const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
          const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
          
          const hoursDisplay = `${diffHours}h ${diffMins}m`;
          workingHoursSpan.textContent = hoursDisplay;
          workingHoursSpan.style.color = '#10b981';
        } else if (outTime < inTime) {
          workingHoursSpan.textContent = 'Invalid';
          workingHoursSpan.style.color = '#ef4444';
        }
      } else {
        workingHoursSpan.textContent = '—';
        workingHoursSpan.style.color = '#64748b';
      }
      
      console.log(`Employee ${employeeId} - Day ${day} - ${type === 'in' ? 'In Time' : 'Out Time'}: ${time}`);
    });
  });

  // Month navigation function
  function navigateMonth(year, month) {
    // Redirect to the same page with different month and year parameters
    const url = new URL(window.location);
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    window.location.href = url.toString();
  }

  // Modal Management Functions
  let currentModalData = {
    employeeId: null,
    day: null,
    employeeName: null
  };

  // Get employee name from table
  function getEmployeeNameForId(employeeId) {
    const checkboxes = document.querySelectorAll(`.attendance-checkbox[data-employee-id="${employeeId}"]`);
    if (checkboxes.length > 0) {
      const row = checkboxes[0].closest('tr');
      if (row) {
        const nameCell = row.querySelector('td:first-child');
        if (nameCell) {
          return nameCell.textContent.trim();
        }
      }
    }
    return 'Unknown Employee';
  }

  // Open Edit Modal
  function openEditModal(employeeId, day, attendance, tasklog, inTime, outTime) {
    console.log('Opening modal with:', {employeeId, day, attendance, tasklog, inTime, outTime});
    
    currentModalData = {
      employeeId: employeeId,
      day: day,
      employeeName: getEmployeeNameForId(employeeId)
    };

    // Populate modal with current data
    document.getElementById('modalEmployeeName').textContent = currentModalData.employeeName;
    document.getElementById('modalDate').textContent = new Date(CURRENT_YEAR, CURRENT_MONTH - 1, day).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    
    document.getElementById('modalAttendance').checked = attendance;
    document.getElementById('modalTasklog').checked = tasklog;
    document.getElementById('modalInTime').value = inTime || '';
    document.getElementById('modalOutTime').value = outTime || '';

    console.log('Modal populated. Current values in inputs:', {
      modalInTime: document.getElementById('modalInTime').value,
      modalOutTime: document.getElementById('modalOutTime').value
    });

    // Show modal
    document.getElementById('editAttendanceModal').style.display = 'flex';
  }

  // Close Modal
  function closeEditModal() {
    document.getElementById('editAttendanceModal').style.display = 'none';
    currentModalData = { employeeId: null, day: null, employeeName: null };
  }

  // Modal Event Listeners
  document.getElementById('closeModalBtn').addEventListener('click', closeEditModal);
  document.getElementById('modalCancelBtn').addEventListener('click', closeEditModal);

  // Close modal when clicking outside
  document.getElementById('editAttendanceModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeEditModal();
    }
  });

  // Handle modal form submission
  document.getElementById('editAttendanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const employeeId = currentModalData.employeeId;
    const day = currentModalData.day;
    const inTime = document.getElementById('modalInTime').value;
    const outTime = document.getElementById('modalOutTime').value;
    const attendance = document.getElementById('modalAttendance').checked;
    const tasklog = document.getElementById('modalTasklog').checked;

    // Validate times if both are provided
    if (inTime && outTime) {
      const inTimeObj = new Date(`2000-01-01 ${inTime}`);
      const outTimeObj = new Date(`2000-01-01 ${outTime}`);
      if (outTimeObj <= inTimeObj) {
        showAlert(`Invalid times: Out time must be after in time`, 'error');
        return;
      }
    }

    const attendanceData = [{
      employee_id: employeeId,
      records: [{
        day: day,
        attendance: attendance,
        in_time: inTime || null,
        out_time: outTime || null,
        tasklog_submitted: tasklog
      }]
    }];

    console.log('Submitting from modal:', attendanceData);

    try {
      const response = await fetch('attendance_handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(attendanceData)
      });

      const result = await response.json();
      console.log('Response from server:', result);

      if (response.ok && result.success) {
        showAlert(`Attendance updated for ${currentModalData.employeeName}`, 'success');
        
        // Update button UI immediately without reload
        const button = document.querySelector(`.day-update-btn[data-employee-id="${currentModalData.employeeId}"][data-day="${currentModalData.day}"]`);
        if (button) {
          button.classList.remove('btn-add');
          button.classList.add('btn-edit');
          button.querySelector('i').className = 'fas fa-edit';
          button.setAttribute('data-has-record', 'true');
          button.title = `Edit attendance for day ${currentModalData.day}`;
        }
        
        // Update the time displays with new values
        const inTimeDisplay = document.querySelector(`.in-time-display[data-employee-id="${currentModalData.employeeId}"][data-day="${currentModalData.day}"]`);
        const outTimeDisplay = document.querySelector(`.out-time-display[data-employee-id="${currentModalData.employeeId}"][data-day="${currentModalData.day}"]`);
        const workingHoursSpan = document.querySelector(`.working-hours[data-employee-id="${currentModalData.employeeId}"][data-day="${currentModalData.day}"]`);
        
        const inTimeInput = document.querySelector(`.in-time[data-employee-id="${currentModalData.employeeId}"][data-day="${currentModalData.day}"]`);
        const outTimeInput = document.querySelector(`.out-time[data-employee-id="${currentModalData.employeeId}"][data-day="${currentModalData.day}"]`);
        
        // Update time displays if times exist
        if (inTimeInput.value && inTimeDisplay) {
          const inTimeObj = new Date(`2000-01-01 ${inTimeInput.value}`);
          inTimeDisplay.textContent = inTimeObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
          inTimeDisplay.style.display = 'block';
        }
        
        if (outTimeInput.value && outTimeDisplay) {
          const outTimeObj = new Date(`2000-01-01 ${outTimeInput.value}`);
          outTimeDisplay.textContent = outTimeObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
          outTimeDisplay.style.display = 'block';
        }
        
        // Update working hours
        if (inTimeInput.value && outTimeInput.value && workingHoursSpan) {
          const inTimeObj = new Date(`2000-01-01 ${inTimeInput.value}`);
          const outTimeObj = new Date(`2000-01-01 ${outTimeInput.value}`);
          if (outTimeObj > inTimeObj) {
            const diffMs = outTimeObj - inTimeObj;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            workingHoursSpan.textContent = `${diffHours}h ${diffMins}m`;
            workingHoursSpan.style.color = '#10b981';
          }
        }
        
        closeEditModal();
      } else {
        showAlert(`Error: ${result.message || 'Failed to update attendance'}`, 'error');
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      showAlert(`Error: ${error.message}`, 'error');
    }
  });

  // Export to Excel
  function exportToExcel() {
    const year = new URLSearchParams(window.location.search).get('year') || new Date().getFullYear();
    const month = new URLSearchParams(window.location.search).get('month') || new Date().getMonth() + 1;
    
    window.location.href = `attendance_export.php?format=excel&year=${year}&month=${month}`;
  }

  // Export to PDF
  function exportToPDF() {
    const year = new URLSearchParams(window.location.search).get('year') || new Date().getFullYear();
    const month = new URLSearchParams(window.location.search).get('month') || new Date().getMonth() + 1;
    
    window.location.href = `attendance_export.php?format=pdf&year=${year}&month=${month}`;
  }
</script>

<?php
$pageContent = ob_get_clean();
include 'layout.php';
?>
