<?php
header('Content-Type: application/json');

require_once 'config.php';
require_once 'auth_check.php';

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'fetch':
        fetchTeamMembers($conn);
        break;
    case 'add':
        addTeamMember($conn);
        break;
    case 'edit':
        editTeamMember($conn);
        break;
    case 'delete':
        deleteTeamMember($conn);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();

// Fetch all team members
function fetchTeamMembers($conn) {
    $sql = "SELECT id, name, designation, image FROM team_members ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if ($result) {
        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $members]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// Add new team member
function addTeamMember($conn) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $designation = $conn->real_escape_string($_POST['designation'] ?? '');
    $image = $_POST['image'] ?? '';
    
    if (empty($name) || empty($designation)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name and designation are required']);
        return;
    }
    
    $sql = "INSERT INTO team_members (name, designation, image) VALUES ('$name', '$designation', '$image')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Team member added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add team member']);
    }
}

// Edit team member
function editTeamMember($conn) {
    $id = intval($_POST['id'] ?? 0);
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $designation = $conn->real_escape_string($_POST['designation'] ?? '');
    $image = $_POST['image'] ?? '';
    
    if ($id <= 0 || empty($name) || empty($designation)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        return;
    }
    
    // If image is provided (new or updated), use it; otherwise keep existing
    if (!empty($image)) {
        $sql = "UPDATE team_members SET name = '$name', designation = '$designation', image = '$image' WHERE id = $id";
    } else {
        $sql = "UPDATE team_members SET name = '$name', designation = '$designation' WHERE id = $id";
    }
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Team member updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update team member']);
    }
}

// Delete team member
function deleteTeamMember($conn) {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        return;
    }
    
    $sql = "DELETE FROM team_members WHERE id = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Team member deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete team member']);
    }
}