<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];

    // Validate project ID
    $project_query = "SELECT * FROM projects WHERE id = ? AND team_leader_id = ?";
    $stmt = $conn->prepare($project_query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Project query preparation failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('ii', $project_id, $user_id);
    $stmt->execute();
    $project_result = $stmt->get_result();

    if ($project_result->num_rows > 0) {
        $chat_query = "SELECT chats.message, chats.created_at, users.username FROM chats JOIN users ON chats.user_id = users.id WHERE chats.project_id = ?";
        $stmt = $conn->prepare($chat_query);
        if ($stmt === false) {
            echo json_encode(['success' => false, 'error' => 'Chat query preparation failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $chat_result = $stmt->get_result();

        $messages = [];
        while ($row = $chat_result->fetch_assoc()) {
            $messages[] = $row;
        }

        echo json_encode(['success' => true, 'messages' => $messages]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid project ID.']);
    }
} elseif (isset($_POST['coworker_id'])) {
    $coworker_id = $_POST['coworker_id'];

    if ($coworker_id == $user_id) {
        echo json_encode(['success' => false, 'error' => 'You cannot chat with yourself.']);
        exit;
    }

    // Fetch chat messages between the current user and the selected coworker
    $chat_query = "SELECT coworker_chats.message, coworker_chats.created_at, users.username 
                   FROM coworker_chats 
                   JOIN users ON coworker_chats.sender_id = users.id OR coworker_chats.receiver_id = users.id 
                   WHERE (coworker_chats.sender_id = ? AND coworker_chats.receiver_id = ?) 
                   OR (coworker_chats.sender_id = ? AND coworker_chats.receiver_id = ?)";
    $stmt = $conn->prepare($chat_query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Coworker chat query preparation failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('iiii', $user_id, $coworker_id, $coworker_id, $user_id);
    $stmt->execute();
    $chat_result = $stmt->get_result();

    $messages = [];
    while ($row = $chat_result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode(['success' => true, 'messages' => $messages]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}

$conn->close();
?>
