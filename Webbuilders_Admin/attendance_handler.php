<?php
require_once 'auth_check.php';
require_once 'config.php';
require_once 'dbConnect.php';

header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors in output
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/attendance_handler.log');

// Log incoming request
error_log('=== Attendance Handler Request ===');
error_log('Method: ' . $_SERVER['REQUEST_METHOD']);
error_log('Timestamp: ' . date('Y-m-d H:i:s'));

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
error_log('Raw input: ' . substr($input, 0, 200)); // Log first 200 chars
$data = json_decode($input, true);

if (!$data) {
    error_log('JSON decode error: ' . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit;
}

try {
    // Create attendance table if not exists
    createAttendanceTable($conn);

    $allSuccess = true;
    $failedRecords = [];
    
    // Default month and year
    $requestMonth = date('m');
    $requestYear = date('Y');

    // Process each employee's records
    foreach ($data as $employeeData) {
        // Skip if not an array/object
        if (!is_array($employeeData) && !is_object($employeeData)) {
            continue;
        }
        
        // Extract month/year from first employee record if provided
        if (isset($employeeData['month'])) {
            $requestMonth = intval($employeeData['month']);
        }
        if (isset($employeeData['year'])) {
            $requestYear = intval($employeeData['year']);
        }
        
        $employeeId = isset($employeeData['employee_id']) ? intval($employeeData['employee_id']) : 0;
        $records = isset($employeeData['records']) ? $employeeData['records'] : [];

        if (!$employeeId || empty($records)) {
            if ($employeeId) {
                $allSuccess = false;
                $failedRecords[] = "Employee ID $employeeId: No valid records provided";
            }
            continue;
        }

        // Verify employee exists and was working during the requested month
        // Employee should have: joining_date <= month_end AND (left_date IS NULL OR left_date > month_start)
        $monthStartDate = sprintf("%04d-%02d-01", $requestYear, $requestMonth);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $requestMonth, $requestYear);
        $monthEndDate = sprintf("%04d-%02d-%02d", $requestYear, $requestMonth, $daysInMonth);
        
        $checkStmt = $conn->prepare("
            SELECT id FROM employees 
            WHERE id = :id 
            AND joining_date <= :month_end 
            AND (left_date IS NULL OR left_date > :month_start)
        ");
        $checkStmt->execute([
            ':id' => $employeeId,
            ':month_start' => $monthStartDate,
            ':month_end' => $monthEndDate
        ]);
        if (!$checkStmt->fetch()) {
            $allSuccess = false;
            $failedRecords[] = "Employee ID $employeeId: Employee was not working during this period";
            continue;
        }

        // Process each day's record
        foreach ($records as $record) {
            $day = isset($record['day']) ? intval($record['day']) : null;
            $attendance = isset($record['attendance']) ? (bool)$record['attendance'] : false;
            $inTime = isset($record['in_time']) && $record['in_time'] ? $record['in_time'] : null;
            $outTime = isset($record['out_time']) && $record['out_time'] ? $record['out_time'] : null;
            $tasklogSubmitted = isset($record['tasklog_submitted']) ? (bool)$record['tasklog_submitted'] : false;

            if (!$day || $day < 1 || $day > 31) {
                $allSuccess = false;
                $failedRecords[] = "Employee $employeeId: Invalid day $day";
                continue;
            }

            // Validate times if provided
            if ($inTime && !validateTime($inTime)) {
                $allSuccess = false;
                $failedRecords[] = "Employee $employeeId Day $day: Invalid in time format";
                continue;
            }

            if ($outTime && !validateTime($outTime)) {
                $allSuccess = false;
                $failedRecords[] = "Employee $employeeId Day $day: Invalid out time format";
                continue;
            }

            // Validate that out time is after in time if both are provided
            if ($inTime && $outTime) {
                $inDateTime = new DateTime("2000-01-01 $inTime");
                $outDateTime = new DateTime("2000-01-01 $outTime");
                if ($outDateTime <= $inDateTime) {
                    $allSuccess = false;
                    $failedRecords[] = "Employee $employeeId Day $day: Out time must be after in time";
                    continue;
                }
            }

            // Save attendance record
            error_log("Saving: Employee $employeeId, Day $day, Month $requestMonth, Year $requestYear");
            try {
                $saved = saveAttendanceRecord($conn, $employeeId, $day, $attendance, $inTime, $outTime, $tasklogSubmitted, $requestMonth, $requestYear);
                if (!$saved) {
                    error_log("Failed to save - function returned false");
                    $allSuccess = false;
                    $failedRecords[] = "Employee $employeeId Day $day: Failed to save record";
                }
            } catch (Exception $e) {
                error_log("Exception in saveAttendanceRecord: " . $e->getMessage());
                $allSuccess = false;
                $failedRecords[] = "Employee $employeeId Day $day: " . $e->getMessage();
            }
        }
    }

    if ($allSuccess) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'All attendance records saved successfully'
        ]);
    } else {
        http_response_code(207);
        echo json_encode([
            'success' => false,
            'message' => 'Some records failed to save',
            'failed_records' => $failedRecords
        ]);
    }

} catch (Exception $e) {
    error_log('EXCEPTION CAUGHT: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

/**
 * Create attendance table if it doesn't exist
 */
function createAttendanceTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        day_of_month INT NOT NULL,
        attendance BOOLEAN DEFAULT FALSE,
        in_time TIME NULL,
        out_time TIME NULL,
        tasklog_submitted BOOLEAN DEFAULT FALSE,
        working_hours DECIMAL(5, 2) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_employee_date (employee_id, attendance_date),
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
        INDEX idx_employee_id (employee_id),
        INDEX idx_attendance_date (attendance_date)
    )";

    try {
        $result = $conn->exec($sql);
        error_log('CREATE TABLE result: ' . ($result !== false ? 'success' : 'failed'));
        if ($result === false) {
            $errorInfo = $conn->errorInfo();
            error_log('PDO Error: ' . print_r($errorInfo, true));
            throw new Exception('Failed to create table: ' . $errorInfo[2]);
        }
    } catch (PDOException $e) {
        error_log('PDO Exception in createAttendanceTable: ' . $e->getMessage());
        throw new Exception('Database error creating table: ' . $e->getMessage());
    }
}

/**
 * Save attendance record (insert or update)
 */
function saveAttendanceRecord($conn, $employeeId, $day, $attendance, $inTime, $outTime, $tasklogSubmitted, $month = null, $year = null) {
    // Use provided month/year or default to current date
    if ($month === null) {
        $month = date('m');
    }
    if ($year === null) {
        $year = date('Y');
    }

    // Build the attendance date
    $attendanceDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
    error_log("Building attendance date: Year=$year, Month=$month, Day=$day => $attendanceDate");

    // Calculate working hours if both times are provided
    $workingHours = null;
    if ($inTime && $outTime) {
        $inDateTime = new DateTime("2000-01-01 $inTime");
        $outDateTime = new DateTime("2000-01-01 $outTime");
        $interval = $inDateTime->diff($outDateTime);
        $workingHours = $interval->h + ($interval->i / 60);
    }

    $sql = "INSERT INTO attendance (employee_id, attendance_date, day_of_month, attendance, in_time, out_time, tasklog_submitted, working_hours)
            VALUES (:employee_id, :attendance_date, :day, :attendance, :in_time, :out_time, :tasklog_submitted, :working_hours)
            ON DUPLICATE KEY UPDATE
            attendance = :attendance,
            in_time = :in_time,
            out_time = :out_time,
            tasklog_submitted = :tasklog_submitted,
            working_hours = :working_hours";

    try {
        error_log("Preparing SQL statement");
        $stmt = $conn->prepare($sql);
        
        $params = [
            ':employee_id' => $employeeId,
            ':attendance_date' => $attendanceDate,
            ':day' => $day,
            ':attendance' => $attendance ? 1 : 0,
            ':in_time' => $inTime,
            ':out_time' => $outTime,
            ':tasklog_submitted' => $tasklogSubmitted ? 1 : 0,
            ':working_hours' => $workingHours
        ];
        
        error_log("Executing with params: " . json_encode($params));
        $result = $stmt->execute($params);
        error_log("Execute result: " . ($result ? "true" : "false"));
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("SQL Error: " . $errorInfo[2]);
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("PDOException in saveAttendanceRecord: " . $e->getMessage());
        throw new Exception("Database error: " . $e->getMessage());
    }
}

/**
 * Validate time format (HH:MM)
 */
function validateTime($time) {
    return preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time) === 1;
}
?>
