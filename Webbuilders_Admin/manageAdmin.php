<?php
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

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'addAdmin':
        addAdmin($conn);
        break;
    case 'updateAdmin':
        updateAdmin($conn);
        break;
    case 'deleteAdmin':
        deleteAdmin($conn);
        break;
    case 'getAdmin':
        getAdmin($conn);
        break;
    case 'getAllAdmins':
        getAllAdmins($conn);
        break;
    case 'updateStatus':
        updateStatus($conn);
        break;
    default:
        http_response_code(400);
        ob_clean();
        die(json_encode(['success' => false, 'message' => 'Invalid action']));
}

$conn->close();

function addAdmin($conn) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT); 
    $role = $conn->real_escape_string($_POST['role'] ?? 'staff');
    $status = $conn->real_escape_string($_POST['status'] ?? 'active');
    
    // Use provided start_date or current time if not provided
    $start_date = isset($_POST['start_date']) && !empty($_POST['start_date']) 
        ? date('Y-m-d H:i:s', strtotime($_POST['start_date'])) 
        : date('Y-m-d H:i:s');
    
    $created_at = $conn->real_escape_string($start_date);
    
    $sql = "INSERT INTO users (name, email, password, role, status, created_at) VALUES ('$name', '$email', '$password', '$role', '$status', '$created_at')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Admin added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add admin: ' . $conn->error]);
    }
}

function updateAdmin($conn) {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $passwordClause = "";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passwordClause = ", password = '$password'";
    }
    
    $dateClause = "";
    if (isset($_POST['start_date']) && !empty($_POST['start_date'])) {
        $start_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
        $dateClause = ", created_at = '$start_date'";
    }

    $sql = "UPDATE  users SET name = '$name', email = '$email', role = '$role', status = '$status' $passwordClause $dateClause WHERE id = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Admin updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update admin: ' . $conn->error]);
    }
}

function updateStatus($conn) {
    $id = $conn->real_escape_string($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE users SET status = '$status' WHERE id = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $conn->error]);
    }
}

function deleteAdmin($conn) {
    $id = $conn->real_escape_string($_POST['id']);
    
    $sql = "DELETE FROM users WHERE id = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Admin deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete admin: ' . $conn->error]);
    }
}

function getAdmin($conn) {
    $id = $conn->real_escape_string($_GET['id']);
    
    $sql = "SELECT id, name, email, role, status, created_at FROM users WHERE id = $id"; // Exclude password
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $admin]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
    }
}

function getAllAdmins($conn) {
    $sql = "SELECT id, name, email, role, status, created_at FROM users WHERE role != 'admin' ORDER BY created_at DESC"; // Exclude password
    $result = $conn->query($sql);
    
    $admins = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $admins]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch admins: ' . $conn->error]);
    }
}
