<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: register.php');
    exit;
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the form data and save it to the database
    $name = $_POST['name'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $zip = $_POST['zip'];
    $address = $_POST['address'];
    $taskDescription = $_POST['taskDescription'];
    $date = $_POST['date'];
    $messageType = $_POST['messageType'];

    // Handle file upload
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_destination = 'uploads/' . $file_name;
    move_uploaded_file($file_tmp, $file_destination);

    $query = "INSERT INTO project_requests (name, last_name, email, phone, zip, address, file, task_description, date, message_type)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssssssss', $name, $lastName, $email, $phone, $zip, $address, $file_destination, $taskDescription, $date, $messageType);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to a thank you page or display a success message
    echo "Project request submitted successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/project_request/project_request.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Request a Project</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="zip">Zip Code:</label>
                <input type="text" class="form-control" id="zip" name="zip" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <div>
                <label for="technical_task">Technical Task (Word or PDF):</label>
                <div class="input-file-container">
                    <input type="file" id="technical_task" name="technical_task" class="input-file" required>
                    <label for="technical_task" class="input-file-label">Choose File</label>
                </div>
            </div>
            <div class="form-group">
                <label for="taskDescription">Task Description:</label><br>
                <textarea class="form-control" id="taskDescription" name="taskDescription" rows="4" maxlength="1000"></textarea>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label>Way to Get a Message:</label><br>
                <input type="radio" id="phoneMessage" name="messageType" value="phone">
                <label for="phoneMessage">By Phone Number</label><br>
                <input type="radio" id="emailMessage" name="messageType" value="email">
                <label for="emailMessage">By Email</label><br>
            </div>
            <button id="submitBtn" class="btn gradient-btn">Send</button>
        </form>
    </div>
</body>
</html>
