<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php';

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
if ($stmt === false) {
    die('Failed to prepare statement (SELECT role): ' . $conn->error);
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'team_leader') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($action === 'accept') {
    // First, manually fetch the data
    $select_query = "SELECT task_description AS name, task_description AS description, user_id, created_at FROM project_requests WHERE id = ?";
    $select_stmt = $conn->prepare($select_query);
    if ($select_stmt === false) {
        die('Failed to prepare select statement: ' . $conn->error . '<br>Query: ' . $select_query);
    }
    $select_stmt->bind_param('i', $project_id);
    $select_stmt->execute();
    $select_result = $select_stmt->get_result();
    $project_data = $select_result->fetch_assoc();
    
    if ($project_data) {
        // Then, insert the fetched data
        $insert_query = "INSERT INTO projects (name, description, team_leader_id, created_at, accepted) VALUES (?, ?, ?, ?, 0)";
        $insert_stmt = $conn->prepare($insert_query);
        if ($insert_stmt === false) {
            die('Failed to prepare insert statement: ' . $conn->error . '<br>Query: ' . $insert_query);
        }
        $insert_stmt->bind_param('ssis', $project_data['name'], $project_data['description'], $project_data['user_id'], $project_data['created_at']);
        if ($insert_stmt->execute()) {
            $delete_query = "DELETE FROM project_requests WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            if ($delete_stmt === false) {
                die('Failed to prepare delete statement: ' . $conn->error . '<br>Query: ' . $delete_query);
            }
            $delete_stmt->bind_param('i', $project_id);
            $delete_stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Project accepted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to accept project: ' . $insert_stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No data found for the specified project ID']);
    }
} elseif ($action === 'deny') {
    $query = "DELETE FROM project_requests WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Failed to prepare delete statement: ' . $conn->error . '<br>Query: ' . $query);
    }
    $stmt->bind_param('i', $project_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Project denied successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to deny project: ' . $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$stmt->close();
$conn->close();
?>
