<?php session_start(); ?>

<?php
include 'db_connect.php';

$sql = "SELECT username, rating, message FROM ratings";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);

$conn->close();
?>
