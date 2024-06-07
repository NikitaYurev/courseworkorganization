<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$message_id = $_POST['id'];
$new_message = $_POST['message'];

// Ensure the user is the owner of the message
$check_query = "SELECT * FROM chats WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($check_query);
if ($stmt === false) {
    echo json_encode(['success' => false, 'error' => 'Message query preparation failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param('ii', $message_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'You can only edit your own messages.']);
    exit;
}

// Update the message
$update_query = "UPDATE chats SET message = ? WHERE id = ?";
$stmt = $conn->prepare($update_query);
if ($stmt === false) {
    echo json_encode(['success' => false, 'error' => 'Message update preparation failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param('si', $new_message, $message_id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update message.']);
}

$conn->close();
?>
