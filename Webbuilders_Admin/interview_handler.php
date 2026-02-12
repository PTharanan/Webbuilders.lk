<?php
// Set JSON response header
header('Content-Type: application/json');

require_once 'config.php';
require_once 'auth_check.php';

// Database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Response array
$response = ['success' => false, 'message' => ''];

switch ($action) {
    case 'fetch':
        fetchInterviews($conn);
        break;
    case 'add':
        addInterview($conn);
        break;
    case 'edit':
        editInterview($conn);
        break;
    case 'delete':
        deleteInterview($conn);
        break;
    case 'get':
        getInterview($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();

// Fetch all interviews
function fetchInterviews($conn) {
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $sql = "SELECT id, candidate_name, nic, phone_number, interview_date, status, join_date, cv_path, created_at FROM interviews WHERE 1=1";
    
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (candidate_name LIKE '%$search%' OR nic LIKE '%$search%' OR phone_number LIKE '%$search%')";
    }
    
    if (!empty($status)) {
        $status = $conn->real_escape_string($status);
        $sql .= " AND status = '$status'";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $result = $conn->query($sql);
    $interviews = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $interviews[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $interviews]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch interviews']);
    }
}

// Get single interview
function getInterview($conn) {
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid interview ID']);
        return;
    }
    
    $sql = "SELECT * FROM interviews WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $interview = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $interview]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Interview not found']);
    }
    
    $stmt->close();
}

// Add new interview
function addInterview($conn) {
    $candidate_name = $conn->real_escape_string($_POST['candidate_name'] ?? '');
    $nic = $conn->real_escape_string($_POST['nic'] ?? '');
    $phone_number = $conn->real_escape_string($_POST['phone_number'] ?? '');
    $interview_date = $conn->real_escape_string($_POST['interview_date'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? 'pending');
    $join_date = null;
    
    if ($status === 'passed') {
        $join_date = $conn->real_escape_string($_POST['join_date'] ?? null);
    }
    
    // Validate required fields
    if (empty($candidate_name) || empty($nic) || empty($phone_number) || empty($interview_date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Handle file upload
    $cv_path = null;
    if (isset($_FILES['cv_document']) && $_FILES['cv_document']['error'] === UPLOAD_ERR_OK) {
        $cv_path = handleFileUpload($_FILES['cv_document']);
        if (!$cv_path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to upload CV document']);
            return;
        }
    }
    
    $sql = "INSERT INTO interviews (candidate_name, nic, phone_number, interview_date, status, join_date, cv_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param("sssssss", $candidate_name, $nic, $phone_number, $interview_date, $status, $join_date, $cv_path);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Interview added successfully', 'id' => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add interview']);
    }
    
    $stmt->close();
}

// Edit interview
function editInterview($conn) {
    $id = intval($_POST['id'] ?? 0);
    $candidate_name = $conn->real_escape_string($_POST['candidate_name'] ?? '');
    $nic = $conn->real_escape_string($_POST['nic'] ?? '');
    $phone_number = $conn->real_escape_string($_POST['phone_number'] ?? '');
    $interview_date = $conn->real_escape_string($_POST['interview_date'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? 'pending');
    $join_date = null;
    
    if ($status === 'passed') {
        $join_date = $conn->real_escape_string($_POST['join_date'] ?? null);
    }
    
    // Validate
    if ($id <= 0 || empty($candidate_name) || empty($nic) || empty($phone_number) || empty($interview_date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        return;
    }
    
    // Handle file upload if provided
    $cv_path = null;
    if (isset($_FILES['cv_document']) && $_FILES['cv_document']['error'] === UPLOAD_ERR_OK) {
        // Delete old file
        $old_interview = $conn->query("SELECT cv_path FROM interviews WHERE id = $id");
        if ($old_interview && $row = $old_interview->fetch_assoc()) {
            if ($row['cv_path'] && file_exists($row['cv_path'])) {
                unlink($row['cv_path']);
            }
        }
        
        $cv_path = handleFileUpload($_FILES['cv_document']);
        if (!$cv_path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to upload CV document']);
            return;
        }
    }
    
    if ($cv_path) {
        $sql = "UPDATE interviews SET candidate_name=?, nic=?, phone_number=?, interview_date=?, status=?, join_date=?, cv_path=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $candidate_name, $nic, $phone_number, $interview_date, $status, $join_date, $cv_path, $id);
    } else {
        $sql = "UPDATE interviews SET candidate_name=?, nic=?, phone_number=?, interview_date=?, status=?, join_date=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $candidate_name, $nic, $phone_number, $interview_date, $status, $join_date, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Interview updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update interview']);
    }
    
    $stmt->close();
}

// Delete interview
function deleteInterview($conn) {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid interview ID']);
        return;
    }
    
    // Get CV file path to delete
    $result = $conn->query("SELECT cv_path FROM interviews WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['cv_path'] && file_exists($row['cv_path'])) {
            unlink($row['cv_path']);
        }
    }
    
    $sql = "DELETE FROM interviews WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Interview deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete interview']);
    }
    
    $stmt->close();
}

// Handle file upload
function handleFileUpload($file) {
    $upload_dir = './uploads/interviews/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validate file
    if ($file['size'] > $max_size) {
        return false;
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Generate unique filename
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = 'cv_' . time() . '_' . uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    }
    
    return false;
}
?>
