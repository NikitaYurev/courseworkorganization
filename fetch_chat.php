<?php
include 'db_connect.php';

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    $chat_query = "SELECT chats.message, chats.created_at, users.username 
                   FROM chats 
                   JOIN users ON chats.user_id = users.id 
                   WHERE chats.project_id = ? 
                   ORDER BY chats.created_at ASC";
    $chat_stmt = $conn->prepare($chat_query);
    $chat_stmt->bind_param('i', $project_id);
    $chat_stmt->execute();
    $chat_result = $chat_stmt->get_result();
    $chats = $chat_result->fetch_all(MYSQLI_ASSOC);

    foreach ($chats as $chat) {
        echo "<div class='chat-message'>";
        echo "<strong>" . htmlspecialchars($chat['username']) . ":</strong> ";
        echo "<span>" . htmlspecialchars($chat['message']) . "</span>";
        echo "<small class='text-muted'>" . $chat['created_at'] . "</small>";
        echo "</div>";
    }
} else {
    echo "Invalid request.";
}
?>
