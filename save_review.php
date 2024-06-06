<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'], $_POST['user-rating'], $_POST['review-text'])) {
    $user_id = $_SESSION['user_id'];
    $rating = (int)$_POST['user-rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['review-text']);

    $check_review_query = "SELECT * FROM reviews WHERE user_id = ?";
    $stmt = $conn->prepare($check_review_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['message' => "You have already submitted a review.", 'alertType' => "danger"]);
    } else {
        $insert_review_query = "INSERT INTO reviews (user_id, rating, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_review_query);
        if ($stmt) {
            $stmt->bind_param("iis", $user_id, $rating, $comment);
            if ($stmt->execute()) {
                echo json_encode(['message' => "Review submitted successfully", 'alertType' => "success"]);
            } else {
                echo json_encode(['message' => "Error submitting review: " . $stmt->error, 'alertType' => "danger"]);
            }
        } else {
            echo json_encode(['message' => "Error preparing insert statement: " . $conn->error, 'alertType' => "danger"]);
        }
    }
} else {
    echo json_encode(['message' => "Invalid request or missing data", 'alertType' => "danger"]);
}

$conn->close();
?>
