<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
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

if (isset($_POST['id'])) {
    $request_id = $_POST['id'];

    // Move the request to the projects table
    $move_query = "INSERT INTO projects (user_id, name, last_name, email, phone_number, zip_code, address, technical_task, task_description, date, message_method)
                   SELECT user_id, name, last_name, email, phone_number, zip_code, address, technical_task, task_description, date, message_method
                   FROM project_requests WHERE id = ?";
    $stmt = $conn->prepare($move_query);
    $stmt->bind_param('i', $request_id);
    if ($stmt->execute()) {
        // Delete the request from project_requests table
        $delete_query = "DELETE FROM project_requests WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param('i', $request_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete project request']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move project request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
