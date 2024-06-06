<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'], $_POST['user-rating'], $_POST['user-message'])) {
    $user_id = $_SESSION['user_id'];
    $rating = (int)$_POST['user-rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['user-message']);

    $update_query = "UPDATE reviews SET rating = ?, comment = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("isi", $rating, $comment, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['message' => "Review updated successfully", 'alertType' => "success"]);
        } else {
            echo json_encode(['message' => "Error updating review: " . $stmt->error, 'alertType' => "danger"]);
        }
    } else {
        echo json_encode(['message' => "Error preparing update statement: " . $conn->error, 'alertType' => "danger"]);
    }
} else {
    echo json_encode(['message' => "Invalid request or missing data", 'alertType' => "danger"]);
}

$conn->close();
?>
