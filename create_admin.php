<?php
include 'db_connect.php';

$admin_username = 'admin';
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_email = 'admin@example.com';
$admin_role = 'admin';

$checkQuery = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param('s', $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $insertQuery = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('ssss', $admin_username, $admin_email, $admin_password, $admin_role);
    if ($stmt->execute()) {
        echo "Admin user created successfully!";
    } else {
        echo "Failed to create admin user: " . $stmt->error;
    }
} else {
    echo "Admin user already exists!";
}
?>
