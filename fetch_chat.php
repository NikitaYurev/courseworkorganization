<?php
include 'db_connect.php';

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    $chat_query = "SELECT chats.message, chats.created_at, users.username 
                   FROM chats 
                   JOIN users ON chats.user_id = users.id 
                   WHERE chats.project_id = ? 
                   ORDER BY chats.created_at ASC";
    $stmt = $conn->prepare($chat_query);
    $stmt->bind_param('i', $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<p><strong>' . htmlspecialchars($row['username']) . ':</strong> ' . htmlspecialchars($row['message']) . ' <em>(' . $row['created_at'] . ')</em></p>';
        }
    } else {
        echo '<p>No chats found for this project.</p>';
    }
} else {
    echo '<p>Invalid project ID.</p>';
}
?>
