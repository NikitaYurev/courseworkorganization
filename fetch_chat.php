<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

function send_json_response($success, $data = null, $message = '') {
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    send_json_response(false, null, 'User not authenticated.');
}

$user_id = $_SESSION['user_id'];
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : null;
$coworker_id = isset($_POST['coworker_id']) ? intval($_POST['coworker_id']) : null;

if ($project_id) {
    $access_query = "SELECT * FROM projects 
                     LEFT JOIN project_coworkers ON projects.id = project_coworkers.project_id 
                     WHERE (projects.team_leader_id = ? OR project_coworkers.coworker_id = ?) AND projects.id = ?";
    $access_stmt = $conn->prepare($access_query);
    $access_stmt->bind_param('iii', $user_id, $user_id, $project_id);
    $access_stmt->execute();
    $access_result = $access_stmt->get_result();
    if ($access_result->num_rows === 0) {
        send_json_response(false, null, 'Invalid project ID.');
    }

    $messages_query = "SELECT chats.*, users.username FROM chats 
                       JOIN users ON chats.user_id = users.id 
                       WHERE chats.project_id = ? ORDER BY chats.created_at ASC";
    $messages_stmt = $conn->prepare($messages_query);
    if ($messages_stmt === false) {
        send_json_response(false, null, 'Failed to prepare statement: ' . $conn->error);
    }
    $messages_stmt->bind_param('i', $project_id);
    $messages_stmt->execute();
    $messages_result = $messages_stmt->get_result();
    $messages = $messages_result->fetch_all(MYSQLI_ASSOC);

    send_json_response(true, $messages);
}

if ($coworker_id) {
    $messages_query = "SELECT private_chats.*, users.username FROM private_chats 
                       JOIN users ON private_chats.sender_id = users.id 
                       WHERE (private_chats.sender_id = ? AND private_chats.receiver_id = ?) 
                       OR (private_chats.sender_id = ? AND private_chats.receiver_id = ?) 
                       ORDER BY private_chats.created_at ASC";
    $messages_stmt = $conn->prepare($messages_query);
    if ($messages_stmt === false) {
        send_json_response(false, null, 'Failed to prepare statement: ' . $conn->error);
    }
    $messages_stmt->bind_param('iiii', $user_id, $coworker_id, $coworker_id, $user_id);
    $messages_stmt->execute();
    $messages_result = $messages_stmt->get_result();
    $messages = $messages_result->fetch_all(MYSQLI_ASSOC);

    send_json_response(true, $messages);
}

send_json_response(false, null, 'Invalid parameters.');
?>
