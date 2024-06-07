<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$message = $_POST['message'] ?? '';
$project_id = $_POST['project_id'] ?? null;
$coworker_id = $_POST['coworker_id'] ?? null;

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message cannot be empty.']);
    exit;
}

if ($project_id) {
    // Insert project message
    $query = "INSERT INTO chats (project_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('iis', $project_id, $user_id, $message);
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send message.']);
    }
} elseif ($coworker_id) {
    // Insert coworker message
    $query = "INSERT INTO coworker_chats (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('iis', $user_id, $coworker_id, $message);
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send message.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}

$conn->close();
?>
