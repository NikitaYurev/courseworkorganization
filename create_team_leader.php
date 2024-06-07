<?php
include 'db_connect.php';

// Ensure the role column includes 'team_leader'
$alter_enum_query = "ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'coworker', 'user', 'admin', 'team_leader')";
if (!$conn->query($alter_enum_query)) {
    die("Error modifying ENUM: " . $conn->error);
}

// Define the team leader user details
$username = 'team_leader';
$email = 'team_leader@example.com';
$password = password_hash('password123', PASSWORD_BCRYPT); // Ensure to use a strong password in production
$role = 'team_leader';

// Check if the team_leader user already exists
$check_user_query = "SELECT COUNT(*) AS user_exists FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($check_user_query);
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['user_exists'] > 0) {
    echo "The user 'team_leader' already exists.";
} else {
    // Insert the new team leader user
    $insert_user_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_user_query);
    $stmt->bind_param('ssss', $username, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Team leader user created successfully.";
    } else {
        echo "Error creating team leader user: " . $stmt->error;
    }
}

$conn->close();
?>
