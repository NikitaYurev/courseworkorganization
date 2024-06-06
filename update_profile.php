<?php 
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $bio = $_POST['bio'];

    $query = "UPDATE users SET bio = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $bio, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Profile updated successfully!';
        $_SESSION['alert_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error updating profile!';
        $_SESSION['alert_type'] = 'danger';
    }
    header('Location: profile.php');
    exit;
}
?>
