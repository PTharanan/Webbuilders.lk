<?php
// Disable all output and start clean buffer
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Start output buffering to prevent any output before JSON
ob_start();

// Set JSON response header first
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Clear any buffered output before includes
ob_clean();

// Custom error handler to log errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_msg = "[$errno] $errstr in $errfile:$errline";
    error_log($error_msg);
    return true;
});

// Custom exception handler
set_exception_handler(function($exception) {
    error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine());
    http_response_code(500);
    ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $exception->getMessage()
    ]);
    exit;
});

require_once 'config.php';
require_once 'auth_check.php';

// Clear any output from includes
ob_clean();

// Database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    ob_clean();
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Response array
$response = ['success' => false, 'message' => ''];

switch ($action) {
    case 'fetch':
        fetchEmployees($conn);
        break;
    case 'fetch_working':
        fetchWorkingEmployees($conn);
        break;
    case 'add':
        addEmployee($conn);
        break;
    case 'edit':
        editEmployee($conn);
        break;
    case 'delete':
        deleteEmployee($conn);
        break;
    case 'get':
        getEmployee($conn);
        break;
    case 'suggestions':
        getCandidateSuggestions($conn);
        break;
    case 'candidate':
        getCandidateDetails($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();

// Fetch all employees with optional search and filter
function fetchEmployees($conn) {
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $sql = "SELECT * FROM employees WHERE 1=1";
    
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (name LIKE '%$search%' OR phone_number LIKE '%$search%')";
    }
    
    if (!empty($status)) {
        $status = $conn->real_escape_string($status);
        $sql .= " AND status = '$status'";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $result = $conn->query($sql);
    $employees = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $employees]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch employees']);
    }
}

// Fetch only working employees (for team display)
function fetchWorkingEmployees($conn) {
    $sql = "SELECT id, name, designation, photograph_path FROM employees WHERE status = 'working' OR status = 'Working' ORDER BY name ASC";
    
    $result = $conn->query($sql);
    $employees = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $employees]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch team members']);
    }
}

// Get single employee
function getEmployee($conn) {
    $id = $_GET['id'] ?? 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
        return;
    }
    
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM employees WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $employee]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }
}

// Add new employee
function addEmployee($conn) {
    try {
        error_log('=== ADD EMPLOYEE FUNCTION STARTED ===');
        
        // Get form data
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $phone_number = $conn->real_escape_string($_POST['phone_number'] ?? '');
        $address = $conn->real_escape_string($_POST['address'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $designation = $conn->real_escape_string($_POST['designation'] ?? '');
        $joining_date = $conn->real_escape_string($_POST['joining_date'] ?? '');
        $left_date = $conn->real_escape_string($_POST['left_date'] ?? '');
        $status = $conn->real_escape_string($_POST['status'] ?? 'working');
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $selected_interview_id = $conn->real_escape_string($_POST['selected_interview_id'] ?? '');
        $interview_date = $conn->real_escape_string($_POST['interview_date'] ?? '');
        $nic_number = $conn->real_escape_string($_POST['nic_number'] ?? '');
        $verified_by = $conn->real_escape_string($_POST['verified_by'] ?? '');
        $interview_cv_path = $conn->real_escape_string($_POST['interview_cv_path'] ?? '');
        
        error_log('Form data - Name: ' . $name . ', Phone: ' . $phone_number);
        
        // Validate required fields
        if (empty($name) || empty($phone_number)) {
            error_log('Validation failed: Name or phone empty');
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Name and phone number are required']);
            return;
        }
    
    // Create folder name
        $folder_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name) . '_' . preg_replace('/[^0-9]/', '', $phone_number);
        $upload_dir = "uploads/employees/$folder_name/";
        
        error_log('Upload directory: ' . $upload_dir);
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception('Failed to create upload directory: ' . $upload_dir);
            }
            error_log('Created directory: ' . $upload_dir);
        }
        
        // Handle file uploads
        $file_paths = uploadDocuments($upload_dir);
        error_log('File paths: ' . json_encode($file_paths));
        
        // If no cv_resume file was uploaded but interview has a CV, use the interview CV
        if (empty($file_paths['cv_resume']) && !empty($interview_cv_path)) {
            $file_paths['cv_resume'] = $interview_cv_path;
            error_log('Using interview CV path: ' . $interview_cv_path);
        }
        
        // Insert into database
        $sql = "INSERT INTO employees (
            name, phone_number, address, email, designation, joining_date, left_date, 
            status, description, national_id_path, character_certificate_path, 
            bank_proof_path, cv_resume_path, appointment_letter_path, photograph_path, folder_name, 
            interview_date, nic_number, verified_by, ol_result_path, al_result_path
        ) VALUES (
            '$name', '$phone_number', '$address', '$email', '$designation', " . 
            ($joining_date ? "'$joining_date'" : "NULL") . ", " . 
            ($left_date ? "'$left_date'" : "NULL") . ", 
            '$status', '$description', '{$file_paths['national_id']}', '{$file_paths['character_certificate']}', 
            '{$file_paths['bank_proof']}', '{$file_paths['cv_resume']}', '{$file_paths['appointment_letter']}', 
            '{$file_paths['photograph']}', '$folder_name', " . 
            ($interview_date ? "'$interview_date'" : "NULL") . ", '$nic_number', '$verified_by', " . 
            "'{$file_paths['ol_result']}', '{$file_paths['al_result']}'
        )";
        
        error_log('SQL Query: ' . substr($sql, 0, 200) . '...');
        
        if ($conn->query($sql)) {
            $employee_id = $conn->insert_id;
            error_log('Employee inserted with ID: ' . $employee_id);
            
            // If interview was selected, delete it from interviews table
            if (!empty($selected_interview_id)) {
                $conn->query("DELETE FROM interviews WHERE id = $selected_interview_id");
                error_log('Deleted interview ID: ' . $selected_interview_id);
            }
            
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Employee added successfully', 'id' => $employee_id]);
        } else {
            error_log('Database error: ' . $conn->error);
            throw new Exception('Failed to add employee: ' . $conn->error);
        }
    } catch (Exception $e) {
        error_log('Exception in addEmployee: ' . $e->getMessage());
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// Edit employee
function editEmployee($conn) {
    try {
        error_log('=== EDIT EMPLOYEE FUNCTION STARTED ===');
        
        $id = $conn->real_escape_string($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            error_log('Invalid employee ID: ' . $id);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
            return;
        }
        
        // Get existing employee data
        $result = $conn->query("SELECT * FROM employees WHERE id = $id");
        if (!$result || $result->num_rows == 0) {
            error_log('Employee not found with ID: ' . $id);
            throw new Exception('Employee not found');
        }
        
        $existing = $result->fetch_assoc();
        $old_folder = $existing['folder_name'];
        
        // Get form data
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $phone_number = $conn->real_escape_string($_POST['phone_number'] ?? '');
        $address = $conn->real_escape_string($_POST['address'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $designation = $conn->real_escape_string($_POST['designation'] ?? '');
        $joining_date = $conn->real_escape_string($_POST['joining_date'] ?? '');
        $left_date = $conn->real_escape_string($_POST['left_date'] ?? '');
        $status = $conn->real_escape_string($_POST['status'] ?? 'working');
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $interview_date = $conn->real_escape_string($_POST['interview_date'] ?? '');
        $nic_number = $conn->real_escape_string($_POST['nic_number'] ?? '');
        $verified_by = $conn->real_escape_string($_POST['verified_by'] ?? '');
        
        error_log('Editing employee ID: ' . $id . ', Name: ' . $name);
        
        // Create new folder name
        $folder_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name) . '_' . preg_replace('/[^0-9]/', '', $phone_number);
        $upload_dir = "uploads/employees/$folder_name/";
        
        // If folder name changed, rename the folder
        if ($old_folder != $folder_name && file_exists("uploads/employees/$old_folder/")) {
            if (!rename("uploads/employees/$old_folder/", $upload_dir)) {
                throw new Exception('Failed to rename upload directory');
            }
            error_log('Renamed folder from ' . $old_folder . ' to ' . $folder_name);
        } elseif (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }
        
        // Handle file uploads and deletions
        $file_paths = uploadDocuments($upload_dir, $existing);
        
        // Update database
        $sql = "UPDATE employees SET 
            name = '$name',
            phone_number = '$phone_number',
        address = '$address',
        email = '$email',
        designation = '$designation',
        joining_date = " . ($joining_date ? "'$joining_date'" : "NULL") . ",
        left_date = " . ($left_date ? "'$left_date'" : "NULL") . ",
        status = '$status',
        description = '$description',
        national_id_path = '{$file_paths['national_id']}',
        character_certificate_path = '{$file_paths['character_certificate']}',
        bank_proof_path = '{$file_paths['bank_proof']}',
        cv_resume_path = '{$file_paths['cv_resume']}',
        appointment_letter_path = '{$file_paths['appointment_letter']}',
        photograph_path = '{$file_paths['photograph']}',
        folder_name = '$folder_name',
        interview_date = " . ($interview_date ? "'$interview_date'" : "NULL") . ",
        nic_number = '$nic_number',
        verified_by = '$verified_by',
        ol_result_path = '{$file_paths['ol_result']}',
        al_result_path = '{$file_paths['al_result']}'
        WHERE id = $id";
        
        error_log('Update SQL prepared for employee ID: ' . $id);
        
        if ($conn->query($sql)) {
            error_log('Employee updated successfully, ID: ' . $id);
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
        } else {
            error_log('Database update error: ' . $conn->error);
            throw new Exception('Failed to update employee: ' . $conn->error);
        }
    } catch (Exception $e) {
        error_log('Exception in editEmployee: ' . $e->getMessage());
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// Delete employee
function deleteEmployee($conn) {
    $id = $conn->real_escape_string($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
        return;
    }
    
    // Get employee data to delete folder
    $result = $conn->query("SELECT folder_name FROM employees WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        $folder_path = "uploads/employees/{$employee['folder_name']}/";
        
        // Delete folder and all files
        if (file_exists($folder_path)) {
            deleteDirectory($folder_path);
        }
        
        // Delete from database
        if ($conn->query("DELETE FROM employees WHERE id = $id")) {
            echo json_encode(['success' => true, 'message' => 'Employee deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete employee']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }
}

// Upload documents
function uploadDocuments($upload_dir, $existing = null) {
    $file_fields = [
        'national_id' => 'national_id_path',
        'character_certificate' => 'character_certificate_path',
        'bank_proof' => 'bank_proof_path',
        'cv_resume' => 'cv_resume_path',
        'appointment_letter' => 'appointment_letter_path',
        'photograph' => 'photograph_path',
        'ol_result' => 'ol_result_path',
        'al_result' => 'al_result_path'
    ];
    
    $file_paths = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    foreach ($file_fields as $field => $db_field) {
        $file_paths[$field] = $existing[$db_field] ?? '';
        
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $file = $_FILES[$field];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file
            if (!in_array($file_ext, $allowed_extensions)) {
                continue;
            }
            
            if ($file['size'] > $max_size) {
                continue;
            }
            
            // Delete old file if exists
            if (!empty($file_paths[$field]) && file_exists($file_paths[$field])) {
                unlink($file_paths[$field]);
            }
            
            // Upload new file
            $new_filename = $field . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $file_paths[$field] = $target_path;
            }
        }
    }
    
    return $file_paths;
}

// Delete directory recursively
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}

// Get candidate suggestions from interviews table
function getCandidateSuggestions($conn) {
    $search = $_GET['query'] ?? '';
    
    // Get candidates from interviews table where:
    // 1. Status is 'passed'
    // 2. Name matches the search query (if provided)
    $sql = "SELECT i.id, i.candidate_name, i.nic, i.phone_number, i.interview_date, i.join_date, i.cv_path, i.status
            FROM interviews i
            WHERE i.status = 'passed'";
    
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND i.candidate_name LIKE '%$search%'";
    }
    
    $sql .= " ORDER BY i.candidate_name ASC
              LIMIT 10";
    
    $result = $conn->query($sql);
    $candidates = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $candidates]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch suggestions: ' . $conn->error]);
    }
}

// Get candidate details from interviews table
function getCandidateDetails($conn) {
    $interviewId = $_GET['interview_id'] ?? 0;
    
    if ($interviewId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid interview ID']);
        return;
    }
    
    $interviewId = $conn->real_escape_string($interviewId);
    $sql = "SELECT id, candidate_name, nic, phone_number, interview_date, cv_path 
            FROM interviews 
            WHERE id = $interviewId AND status = 'passed'";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $candidate = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $candidate]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Candidate not found']);
    }
}
