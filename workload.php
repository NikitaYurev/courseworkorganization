<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

if (!in_array($user['role'], ['coworker', 'owner', 'admin'])) {
    echo "Access denied.";
    exit;
}

// Fetch projects assigned to the user
$projects_query = "SELECT * FROM projects WHERE team_leader_id = ?";
$projects_stmt = $conn->prepare($projects_query);
$projects_stmt->bind_param('i', $user_id);
$projects_stmt->execute();
$projects_result = $projects_stmt->get_result();
$projects = $projects_result->fetch_all(MYSQLI_ASSOC);

// Fetch coworkers for the projects
$coworkers_query = "SELECT users.id, users.username, project_coworkers.project_id 
                    FROM users 
                    JOIN project_coworkers ON users.id = project_coworkers.coworker_id
                    WHERE project_coworkers.project_id IN (SELECT id FROM projects WHERE team_leader_id = ?)";
$coworkers_stmt = $conn->prepare($coworkers_query);
$coworkers_stmt->bind_param('i', $user_id);
$coworkers_stmt->execute();
$coworkers_result = $coworkers_stmt->get_result();
$coworkers = $coworkers_result->fetch_all(MYSQLI_ASSOC);

// Fetch coworkers, admins, and owners
$users_query = "SELECT id, username FROM users WHERE role IN ('coworker', 'admin', 'owner') AND id != ?";
$users_stmt = $conn->prepare($users_query);
$users_stmt->bind_param('i', $user_id);
$users_stmt->execute();
$users_result = $users_stmt->get_result();
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Fetch projects
$projects_query = "SELECT id, name FROM projects";
$projects_result = $conn->query($projects_query);
$projects = [];
while ($row = $projects_result->fetch_assoc()) {
    $projects[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/workload/workload.css">
    <title>Workload</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Menu</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/index.php' ? 'active' : '') ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/about.php' ? 'active' : '') ?>" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/contact.php' ? 'active' : '') ?>" href="contact.php">Contact</a>
                    </li>
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/administration_page.php' ? 'active' : '') ?>" href="administration_page.php">Administration</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($_SESSION['role'], ['coworker', 'owner', 'admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="workload.php">Workload</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Logged in as <?= htmlspecialchars($_SESSION['username']) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row" id="workload-container">
            <div class="col-md-3" id="projects-list">
                <!-- List of accepted projects by team leader (chat list) -->
                <?php foreach ($projects as $project): ?>
                    <div class="project-item" data-project-id="<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-6 d-flex flex-column" id="chat-section">
                <!-- Chat section (initially empty) -->
                <div class="chat-box flex-grow-1">
                    <p>Select a project to view the chat.</p>
                </div>
                <div id="message-input-container">
                    <textarea id="message-input" rows="1" placeholder="Type your message..."></textarea>
                    <button id="send-message-btn">Send</button>
                </div>
            </div>
            <div class="col-md-3" id="workers-list">
                <!-- List of workers to write to (coworkers list with chats) -->
                <?php foreach ($users as $user): ?>
                    <div class="worker-item" data-coworker-id="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="./js/workload/workload.js"></script>
</body>
</html>

