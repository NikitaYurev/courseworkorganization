<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$message = $_POST['message'];

if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];

    $query = "INSERT INTO chats (project_id, user_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $project_id, $user_id, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message.']);
    }
} elseif (isset($_POST['coworker_id'])) {
    $coworker_id = $_POST['coworker_id'];

    if ($coworker_id == $user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot chat with yourself.']);
        exit;
    }

    $query = "INSERT INTO coworker_chats (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $user_id, $coworker_id, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
