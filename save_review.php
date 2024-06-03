<?php
session_start();

include 'db_connect.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // Retrieve user ID, rating, and comment from the form
        $user_id = $_SESSION['user_id'];
        $rating = $_POST['user-rating'];
        $comment = mysqli_real_escape_string($conn, $_POST['user-message']);

        // Insert the review into the database
        $insert_review_query = "INSERT INTO reviews (user_id, rating, comment) VALUES ('$user_id', '$rating', '$comment')";
        if ($conn->query($insert_review_query) === TRUE) {
            echo "Review submitted successfully";
        } else {
            echo "Error: " . $insert_review_query . "<br>" . $conn->error;
        }
    } else {
        echo "Error: User is not logged in";
    }
}

// Close the database connection
$conn->close();
?>
