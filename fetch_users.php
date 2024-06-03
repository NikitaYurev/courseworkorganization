<?php
// fetch_users.php - this file handle the process
// of retrieving the updated list of users from
// the database and returning it as HTML for insertion
// into the user list table.

$host = 'localhost';
$user = 'root';
$pass = 'Nikitka290620041!';
$dbnm = 'organizationdb';

$conn = new mysqli($host, $user, $pass, $dbnm);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username, email FROM users ORDER BY username";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row["username"]) . "</td><td>" . htmlspecialchars($row["email"]) . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='2'>No users found</td></tr>";
}

$conn->close();
?>
