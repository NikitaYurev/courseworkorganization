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

if ($stmt === false) {
    die('Failed to prepare statement: ' . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'team_leader') {
    echo "Access denied.";
    exit;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Projects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/accept_project/accept_project.css">
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
                    <?php if (in_array($_SESSION['role'], ['coworker', 'owner', 'admin', 'team_leader'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/workload.php' ? 'active' : '') ?>" href="workload.php">Workload</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'team_leader'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/accept_project.php' ? 'active' : '') ?>" href="accept_project.php">Accept Projects</a>
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
                            <a class="nav-link <?= ($_SERVER['PHP_SELF'] == '/register.php' ? 'active' : '') ?>" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="mt-4">
        <div class="container-fluid">
            <div class="row" id="accept-project-container">
                <!-- Left side -->
                <div class="col-md-6" id="project-list">
                    <div class="rounded-box p-3">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="search-input" placeholder="Search projects by name or email" aria-label="Search" aria-describedby="button-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="search-button">
                                    <img src="./img/administration_page/loupe-icon.png" alt="Search" width="20" height="20">
                                </button>
                            </div>
                        </div>
                        
                        <!-- List of project requests goes here -->
                        <div class="project-list-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Project Name</th>
                                        <th scope="col">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php 
                                        // Fetch project requests
                                        $stmt = $conn->prepare("SELECT id, task_description, email FROM project_requests");
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Output project request data while escaping HTML characters
                                        if ($result !== false && $result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr data-project-id='" . htmlspecialchars($row["id"]) . "'>";
                                                echo "<td>" . htmlspecialchars($row["task_description"]) . "</td>";
                                                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='2'>No project requests found</td></tr>";
                                        }

                                        $stmt->close();
                                    ?>
                                </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Right side -->
                <div class="col-md-6" id="project-details">
                    <div class="rounded-box p-3">
                        <div class="form-group">
                            <label for="project-name">Project Name</label>
                            <input type="text" class="form-control" id="project-name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="project-email">Email</label>
                            <input type="email" class="form-control" id="project-email" readonly>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" id="accept-button">Accept</button>
                            <button type="button" class="btn btn-danger" id="deny-button">Deny</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    $('.project-list-container tr').click(function() {
        $('.project-list-container tr').removeClass('selected');
        $(this).addClass('selected');
        var projectName = $(this).find('td:eq(0)').text();
        var projectEmail = $(this).find('td:eq(1)').text();
        $('#project-name').val(projectName);
        $('#project-email').val(projectEmail);
        $('#accept-button').data('project-id', $(this).data('project-id'));
        $('#deny-button').data('project-id', $(this).data('project-id'));
    });

    $('#accept-button').click(function() {
        var projectId = $(this).data('project-id');
        if (projectId) {
            $.post('accept_project_action.php', { id: projectId, action: 'accept' }, function(response) {
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            }, 'json');
        } else {
            alert('Please select a project to accept.');
        }
    });

    $('#deny-button').click(function() {
        var projectId = $(this).data('project-id');
        if (projectId) {
            $.post('accept_project_action.php', { id: projectId, action: 'deny' }, function(response) {
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            }, 'json');
        } else {
            alert('Please select a project to deny.');
        }
    });
});
</script>
</body>
</html>

<?php
// Close connection at the end of the file
if ($conn) {
    $conn->close();
}
?>
