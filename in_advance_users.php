<?php
// IMPORTANT - this code is just to create accounts of owner and his employees,
// so they will be in a system already.

$host = 'localhost';
$user = 'root';
$pass = 'Nikitka290620041!';
$dbname = 'organizationdb';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert owner data
$owner_username = 'owner';
$owner_email = 'owner@example.com';
$owner_password = password_hash('owner123', PASSWORD_DEFAULT);
$owner_role = 'owner';

$sql_owner = "INSERT INTO users (username, email, password, role) VALUES ('$owner_username', '$owner_email', '$owner_password', '$owner_role')";
if ($conn->query($sql_owner) === TRUE) {
    echo "Owner record created successfully<br>";
} else {
    echo "Error creating owner record: " . $conn->error . "<br>";
}

// Insert employee data
$employees = array(
    array('employee1', 'employee1@example.com', 'employee123', 'coworker'),
    array('employee2', 'employee2@example.com', 'employee123', 'coworker'),
    array('employee3', 'employee3@example.com', 'employee123', 'coworker'),
);

foreach ($employees as $employee) {
    $employee_username = $employee[0];
    $employee_email = $employee[1];
    $employee_password = password_hash($employee[2], PASSWORD_DEFAULT);
    $employee_role = $employee[3];

    $sql_employee = "INSERT INTO users (username, email, password, role) VALUES ('$employee_username', '$employee_email', '$employee_password', '$employee_role')";
    if ($conn->query($sql_employee) === TRUE) {
        echo "Employee record created successfully: $employee_username<br>";
    } else {
        echo "Error creating employee record: " . $conn->error . "<br>";
    }

}


// Close connection
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
</head>
<body>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>