<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php'; // Adjust this path as needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'], $_POST['user-rating'], $_POST['user-message'])) {
        $user_id = $_SESSION['user_id'];
        $rating = $_POST['user-rating'];
        $comment = mysqli_real_escape_string($conn, $_POST['user-message']);

        $check_review_query = "SELECT * FROM reviews WHERE user_id = '$user_id'";
        $result = $conn->query($check_review_query);

        if ($result->num_rows > 0) {
            echo json_encode(['message' => "You have already submitted a review. You can update your review in your personal cabinet.", 'alertType' => "danger"]);
        } else {
            $insert_review_query = "INSERT INTO reviews (user_id, rating, comment) VALUES ('$user_id', '$rating', '$comment')";
            if ($conn->query($insert_review_query) === TRUE) {
                echo json_encode(['message' => "Review submitted successfully", 'alertType' => "success"]);
            } else {
                echo json_encode(['message' => "Error submitting review: " . $conn->error, 'alertType' => "danger"]);
            }
        }
    } else {
        echo json_encode(['message' => "User is not logged in, please log in to submit a review", 'alertType' => "danger"]);
    }
} else {
    echo json_encode(['message' => "Invalid request", 'alertType' => "danger"]);
}

$conn->close();
?>
