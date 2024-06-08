<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php'; // Ensure this includes your database connection setup

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$project_id = $_POST['id'];
$action = $_POST['action'];

$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'team_leader') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($action === 'accept') {
    $query = "INSERT INTO projects (user_id, task_description, email)
              SELECT user_id, task_description, email FROM project_requests WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $project_id);
    if ($stmt->execute()) {
        $delete_query = "DELETE FROM project_requests WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param('i', $project_id);
        $delete_stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Project accepted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to accept project']);
    }
} elseif ($action === 'deny') {
    $query = "DELETE FROM project_requests WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $project_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Project denied successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to deny project']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$stmt->close();
$conn->close();
?>
