<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

function send_json_response($success, $message = '') {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    send_json_response(false, 'User not authenticated.');
}

$user_id = $_SESSION['user_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : null;
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : null;
$coworker_id = isset($_POST['coworker_id']) ? intval($_POST['coworker_id']) : null;

if (empty($message)) {
    send_json_response(false, 'Message cannot be empty.');
}

if ($project_id) {
    $access_query = "SELECT * FROM projects 
                     LEFT JOIN project_coworkers ON projects.id = project_coworkers.project_id 
                     WHERE (projects.team_leader_id = ? OR project_coworkers.coworker_id = ?) AND projects.id = ?";
    $access_stmt = $conn->prepare($access_query);
    $access_stmt->bind_param('iii', $user_id, $user_id, $project_id);
    $access_stmt->execute();
    $access_result = $access_stmt->get_result();
    if ($access_result->num_rows === 0) {
        send_json_response(false, 'Invalid project ID.');
    }

    $insert_query = "INSERT INTO chats (project_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    if ($insert_stmt === false) {
        send_json_response(false, 'Failed to prepare statement: ' . $conn->error);
    }
    $insert_stmt->bind_param('iis', $project_id, $user_id, $message);
    if ($insert_stmt->execute()) {
        send_json_response(true, 'Message sent successfully.');
    } else {
        send_json_response(false, 'Failed to send message.');
    }
}

if ($coworker_id) {
    $insert_query = "INSERT INTO private_chats (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    if ($insert_stmt === false) {
        send_json_response(false, 'Failed to prepare statement: ' . $conn->error);
    }
    $insert_stmt->bind_param('iis', $user_id, $coworker_id, $message);
    if ($insert_stmt->execute()) {
        send_json_response(true, 'Message sent successfully.');
    } else {
        send_json_response(false, 'Failed to send message.');
    }
}

send_json_response(false, 'Invalid parameters.');
?>
