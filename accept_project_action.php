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

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'team_leader') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if (isset($_POST['id']) && isset($_POST['action'])) {
    $request_id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // Move the request to the projects table
        $move_query = "INSERT INTO projects (user_id, name, last_name, email, phone_number, zip_code, address, technical_task, task_description, date, message_method)
                       SELECT user_id, name, last_name, email, phone_number, zip_code, address, technical_task, task_description, date, message_method
                       FROM project_requests WHERE id = ?";
        $stmt = $conn->prepare($move_query);

        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param('i', $request_id);
        if ($stmt->execute()) {
            // Delete the request from project_requests table
            $delete_query = "DELETE FROM project_requests WHERE id = ?";
            $stmt = $conn->prepare($delete_query);

            if ($stmt === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
                exit;
            }

            $stmt->bind_param('i', $request_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Project accepted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete project request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move project request']);
        }
    } elseif ($action === 'deny') {
        // Delete the request from project_requests table
        $delete_query = "DELETE FROM project_requests WHERE id = ?";
        $stmt = $conn->prepare($delete_query);

        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param('i', $request_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Project denied successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete project request']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid project ID or action']);
}

// Check if $conn is available before attempting to close it
if ($conn) {
    $conn->close();
}
?>
