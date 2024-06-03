<?php
session_start();
require_once 'db_connect.php';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($searchTerm)) {
    $searchTerm = "%$searchTerm%";
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY username");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} else {
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users ORDER BY username");
}

$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    echo "Error: " . $conn->error;
} else {
    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/administration_page/administration_page.css">
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
                </ul>
                <ul class="navbar-nav ml-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Logged in as <?= htmlspecialchars($_SESSION['username']) ?></a>
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
            <div class="row">
                <!-- Left side -->
                <div class="col-md-6">
                    <div class="rounded-box p-3">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="search-input" placeholder="Search users by name or email" aria-label="Search" aria-describedby="button-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="search-button">
                                    <img src="./img/administration_page/loupe-icon.png" alt="Search" width="20" height="20">
                                </button>
                            </div>
                        </div>
                        
                        <!-- List of users goes here -->
                        <div class="user-list-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php 
                                        // Output user data while escaping HTML characters
                                        if ($result !== false && $result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr data-user-id='" . htmlspecialchars($row["id"]) . "' data-role='" . htmlspecialchars($row["role"]) . "'>";
                                                echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                                                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='2'>No users found</td></tr>";
                                        }
                                    ?>
                                </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Right side -->
                <div class="col-md-6">
                    <div class="rounded-box p-3">
                        <form id="user-form" action="administration.php" method="post">
                            <input type="hidden" id="id" name="id"> <!-- user's id in database -->
                            <input type="hidden" id="old_username" name="old_username">
                            <input type="hidden" id="old_email" name="old_email">
                            <input type="hidden" id="role">
                            <div class="form-group">
                                <label for="username">Name</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <div class="invalid-feedback">Name field is empty, please fill all fields and don't leave any of them empty!</div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Email field is empty, please fill all fields and don't leave any of them empty!</div>
                                <div class="invalid-feedback" id="email-invalid-feedback">Email format is invalid, please enter a valid email address!</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="./js/administration_page/search.js"></script>
    <script src="./js/administration_page/user_management.js"></script>
    <script src="./js/administration_page/rename.js"></script>
    <script src="./js/administration_page/table_stylish.js"></script>
    <script src="./js/administration_page/form_validation.js"></script>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close connection at the end of the file
$conn->close();
?>
