<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php'; // Ensure this path correctly points to your database connection script

// Adjust the SQL query to join the reviews with the users table to fetch usernames
// Assume that your users table has columns 'id' and 'username'
$sql = "SELECT u.username, r.rating, r.comment 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.rating >= 4
        ORDER BY RAND()
        LIMIT 5";

$result = $conn->query($sql);

if (!$result) {
    // If there's an SQL error, output it and exit
    echo json_encode(['message' => 'SQL Error: ' . $conn->error]);
    $conn->close();
    exit;
}

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

if (count($reviews) > 0) {
    // Shuffle the array to randomize which reviews are shown first
    shuffle($reviews);
    $selectedReviews = array_slice($reviews, 0, rand(3, 5)); // Select between 3 and 5 reviews randomly

    // Format the reviews into HTML
    $reviewHTMLs = [];
    foreach ($selectedReviews as $review) {
        $reviewHTML = '<div class="review-box">';
        $reviewHTML .= '<div class="user-name">' . htmlspecialchars($review['username']) . '</div>';
        $reviewHTML .= '<div class="review-content">' . htmlspecialchars($review['comment']) . '</div>';
        $reviewHTML .= '</div>';
        $reviewHTMLs[] = $reviewHTML;
    }

    echo json_encode($reviewHTMLs); // Send the array of formatted HTML reviews
} else {
    echo json_encode(['message' => 'No reviews available.']); // Send a message if no reviews found
}

$conn->close();
?>
