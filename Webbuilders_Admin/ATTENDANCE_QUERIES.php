<?php
/**
 * Attendance System - Useful Queries Reference
 * Copy and paste these queries for common operations
 */

// Note: These are examples. Adjust table/column names as needed.

// ============================================================================
// REPORTING QUERIES
// ============================================================================

// 1. Monthly Attendance Summary
$monthlyAttendance = "
SELECT 
  e.id,
  e.name,
  e.designation,
  COUNT(CASE WHEN a.attendance = 1 THEN 1 END) as days_present,
  COUNT(CASE WHEN a.attendance = 0 THEN 1 END) as days_absent,
  COUNT(a.id) as total_working_days,
  ROUND(SUM(a.working_hours), 2) as total_hours,
  ROUND(AVG(a.working_hours), 2) as avg_daily_hours
FROM employees e
LEFT JOIN attendance a ON e.id = a.employee_id
WHERE YEAR(a.attendance_date) = 2026 
  AND MONTH(a.attendance_date) = 1
  AND e.status = 'working'
GROUP BY e.id, e.name, e.designation
ORDER BY days_present DESC;
";

// 2. Individual Employee Report (with daily details)
$employeeReport = "
SELECT 
  a.day_of_month,
  DATE_FORMAT(a.attendance_date, '%a') as day_name,
  CASE WHEN a.attendance = 1 THEN 'Present' ELSE 'Absent' END as status,
  a.in_time,
  a.out_time,
  a.working_hours,
  CASE WHEN a.tasklog_submitted = 1 THEN 'Yes' ELSE 'No' END as tasklog
FROM attendance a
WHERE a.employee_id = 1
  AND YEAR(a.attendance_date) = 2026 
  AND MONTH(a.attendance_date) = 1
ORDER BY a.day_of_month ASC;
";

// 3. Late Arrivals (after 10:00 AM)
$lateArrivals = "
SELECT 
  e.name,
  a.attendance_date,
  a.in_time,
  TIMEDIFF(a.in_time, '10:00:00') as minutes_late
FROM employees e
JOIN attendance a ON e.id = a.employee_id
WHERE a.in_time > '10:00:00'
  AND YEAR(a.attendance_date) = 2026
ORDER BY a.attendance_date DESC;
";

// 4. Early Departures (before 5:00 PM)
$earlyDepartures = "
SELECT 
  e.name,
  a.attendance_date,
  a.out_time,
  TIMEDIFF('17:00:00', a.out_time) as minutes_early
FROM employees e
JOIN attendance a ON e.id = a.employee_id
WHERE a.out_time < '17:00:00'
  AND a.attendance = 1
ORDER BY a.attendance_date DESC;
";

// 5. Tasks Not Submitted
$missedTasks = "
SELECT 
  e.name,
  COUNT(*) as days_without_tasklog,
  GROUP_CONCAT(a.day_of_month ORDER BY a.day_of_month) as days
FROM employees e
JOIN attendance a ON e.id = a.employee_id
WHERE a.tasklog_submitted = 0
  AND a.attendance = 1
  AND YEAR(a.attendance_date) = 2026
  AND MONTH(a.attendance_date) = 1
GROUP BY e.id, e.name;
";

// ============================================================================
// DATA MANAGEMENT QUERIES
// ============================================================================

// 6. Delete Records for Specific Month
$deleteMonth = "
DELETE FROM attendance
WHERE YEAR(attendance_date) = 2026 
  AND MONTH(attendance_date) = 1;
";

// 7. Update Working Hours for All Records (recalculate)
$recalculateHours = "
UPDATE attendance
SET working_hours = HOUR(TIMEDIFF(out_time, in_time)) + (MINUTE(TIMEDIFF(out_time, in_time)) / 60)
WHERE in_time IS NOT NULL AND out_time IS NOT NULL;
";

// 8. Mark Entire Day as Holiday
$markHoliday = "
INSERT INTO attendance (employee_id, attendance_date, day_of_month, attendance, tasklog_submitted)
SELECT id, '2026-01-26', 26, 0, 1
FROM employees
WHERE status = 'working'
ON DUPLICATE KEY UPDATE attendance = 0;
";

// 9. Copy Previous Month to Current (template)
$copyPreviousMonth = "
INSERT INTO attendance (employee_id, attendance_date, day_of_month, attendance, tasklog_submitted)
SELECT 
  employee_id,
  DATE_ADD(attendance_date, INTERVAL 1 MONTH),
  DAYOFMONTH(DATE_ADD(attendance_date, INTERVAL 1 MONTH)),
  1,
  0
FROM attendance
WHERE YEAR(attendance_date) = 2025 AND MONTH(attendance_date) = 12
ON DUPLICATE KEY UPDATE attendance = 1;
";

// ============================================================================
// ANALYTICS QUERIES
// ============================================================================

// 10. Average Working Hours by Employee (over a period)
$avgHoursByEmployee = "
SELECT 
  e.name,
  COUNT(CASE WHEN a.attendance = 1 THEN 1 END) as days_worked,
  ROUND(AVG(a.working_hours), 2) as avg_daily_hours,
  ROUND(SUM(a.working_hours), 2) as total_hours
FROM employees e
JOIN attendance a ON e.id = a.employee_id
WHERE a.attendance = 1
  AND a.attendance_date BETWEEN '2026-01-01' AND '2026-01-31'
GROUP BY e.id, e.name
ORDER BY avg_daily_hours DESC;
";

// 11. Attendance Percentage
$attendancePercentage = "
SELECT 
  e.name,
  COUNT(CASE WHEN a.attendance = 1 THEN 1 END) as present,
  COUNT(a.id) as total_days,
  ROUND(COUNT(CASE WHEN a.attendance = 1 THEN 1 END) * 100 / COUNT(a.id), 2) as attendance_percentage
FROM employees e
LEFT JOIN attendance a ON e.id = a.employee_id
WHERE YEAR(a.attendance_date) = 2026
GROUP BY e.id, e.name
ORDER BY attendance_percentage DESC;
";

// 12. Overtime Analysis (hours > 9)
$overtimeAnalysis = "
SELECT 
  e.name,
  a.attendance_date,
  a.in_time,
  a.out_time,
  a.working_hours,
  ROUND(a.working_hours - 8, 2) as overtime_hours
FROM employees e
JOIN attendance a ON e.id = a.employee_id
WHERE a.working_hours > 9
ORDER BY a.attendance_date DESC;
";

// ============================================================================
// MAINTENANCE QUERIES
// ============================================================================

// 13. Check Data Integrity
$integrityCheck = "
SELECT 
  'Orphaned Records' as check_type,
  COUNT(*) as count
FROM attendance a
WHERE NOT EXISTS (SELECT 1 FROM employees e WHERE e.id = a.employee_id);
";

// 14. Find Duplicate Entries
$findDuplicates = "
SELECT 
  employee_id,
  attendance_date,
  COUNT(*) as duplicate_count
FROM attendance
GROUP BY employee_id, attendance_date
HAVING COUNT(*) > 1;
";

// 15. Get Table Statistics
$tableStats = "
SELECT 
  COUNT(*) as total_records,
  COUNT(DISTINCT employee_id) as unique_employees,
  MIN(attendance_date) as earliest_record,
  MAX(attendance_date) as latest_record,
  ROUND(AVG(working_hours), 2) as avg_working_hours
FROM attendance;
";

// 16. Optimize Table
$optimize = "
OPTIMIZE TABLE attendance;
ANALYZE TABLE attendance;
";

// 17. Vacuum Table (remove fragmentation)
$vacuum = "
REPAIR TABLE attendance;
OPTIMIZE TABLE attendance;
";

// ============================================================================
// BACKUP & EXPORT QUERIES
// ============================================================================

// 18. Export to CSV Format (use in phpMyAdmin)
$csvExport = "
SELECT 
  CONCAT(e.id) as 'Employee ID',
  e.name as 'Name',
  a.attendance_date as 'Date',
  a.day_of_month as 'Day',
  CASE WHEN a.attendance = 1 THEN 'Present' ELSE 'Absent' END as 'Status',
  a.in_time as 'In Time',
  a.out_time as 'Out Time',
  a.working_hours as 'Working Hours',
  CASE WHEN a.tasklog_submitted = 1 THEN 'Yes' ELSE 'No' END as 'Tasklog'
FROM attendance a
JOIN employees e ON a.employee_id = e.id
WHERE YEAR(a.attendance_date) = 2026
ORDER BY a.attendance_date, e.name
INTO OUTFILE '/tmp/attendance_export.csv'
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\\n';
";

// ============================================================================
// PHP USAGE EXAMPLES
// ============================================================================

?>

<h2>📊 Attendance System - Database Queries</h2>

<p>This file contains commonly used SQL queries for the attendance system.</p>

<h3>Usage in PHP:</h3>
<pre>
&lt;?php
require_once 'dbConnect.php';

// Example: Get monthly summary
$sql = "
SELECT 
  e.name,
  COUNT(CASE WHEN a.attendance = 1 THEN 1 END) as present,
  COUNT(a.id) as total
FROM employees e
LEFT JOIN attendance a ON e.id = a.employee_id
WHERE YEAR(a.attendance_date) = 2026 
  AND MONTH(a.attendance_date) = 1
GROUP BY e.id;
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    echo $row['name'] . ': ' . $row['present'] . ' days present out of ' . $row['total'] . '\n';
}
?&gt;
</pre>

<h3>Using MySQL Command Line:</h3>
<pre>
mysql -u root -p webbuilders_admin
use webbuilders_admin;
PASTE QUERY HERE;
</pre>

<h3>Using phpMyAdmin:</h3>
<ol>
  <li>Open phpMyAdmin</li>
  <li>Select 'webbuilders_admin' database</li>
  <li>Click 'SQL' tab</li>
  <li>Paste query and click 'Go'</li>
</ol>

<p><strong>⚠️ Warning:</strong> Be careful with DELETE and UPDATE queries. Test on a copy first!</p>
