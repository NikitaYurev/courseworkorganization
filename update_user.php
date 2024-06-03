<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $conn->real_escape_string($_POST['id']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    // Update user data in the database
    $sql = "UPDATE users SET username='$username', email='$email' WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "User updated successfully!";
    } else {
        // Debugging information
        echo "Error updating user: " . $conn->error;
        echo "\nSQL: " . $sql;
    }

    $conn->close();
}
?>
