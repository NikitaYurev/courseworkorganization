<?php session_start(); ?>

<?php
include 'db_connect.php';

// Function to create the visits table if it doesn't exist
function createVisitsTable($conn) {
    $create_table_query = "CREATE TABLE IF NOT EXISTS visit_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->query($create_table_query);
}

// Function to record a visit
function recordVisit($conn, $user_id) {
    $insert_visit_query = "INSERT INTO visit_log (id, visit_time) VALUES (?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($insert_visit_query);
    
    if (!$stmt) {
        // Handle the error, for example:
        die('Error in preparing SQL statement: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
}



// Function to record a visit from a guest (no user ID)
function recordGuestVisit($conn) {
    $insert_visit_query = "INSERT INTO visit_log (visit_time) VALUES (CURRENT_TIMESTAMP)";
    $conn->query($insert_visit_query); // No need to prepare if no parameters are involved
}

// Function to count visits in the current week
function getWeeklyVisitCount($conn) {
    $start_of_week = date("Y-m-d H:i:s", strtotime('monday this week'));
    $end_of_week = date("Y-m-d H:i:s", strtotime('sunday this week 23:59:59'));

    $weekly_count_query = "SELECT COUNT(*) as weekly_visits FROM visit_log WHERE visit_time BETWEEN ? AND ?";
    $stmt = $conn->prepare($weekly_count_query);
    $stmt->bind_param('ss', $start_of_week, $end_of_week);
    $stmt->execute();
    $result = $stmt->get_result();
    $weekly_count_row = $result->fetch_assoc();
    return $weekly_count_row['weekly_visits'];
}

// Ensure the visit_log table exists
createVisitsTable($conn);

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Check if the user has already visited today
    $today = date("Y-m-d");
    $visit_check_query = "SELECT * FROM visit_log WHERE visit_log.id = ? AND DATE(visit_time) = ?";
    $stmt = $conn->prepare($visit_check_query);
    if (!$stmt) {
        die('Error in prepare statement: ' . $conn->error);
    }
    $stmt->bind_param('is', $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Record the visit if there is no record for today
        recordVisit($conn, $user_id);
    }
} else {
    // For guests, use a cookie to track visits
    if (!isset($_COOKIE['visited'])) {
        // Record the visit if there is no cookie
        recordGuestVisit($conn);
        // Set a cookie that expires in 1 day
        setcookie('visited', '1', time() + 86400, "/");
    }
}

// Fetch total number of users
$user_count_query = "SELECT COUNT(*) as total_users FROM users";
$user_count_result = $conn->query($user_count_query);

if ($user_count_result) {
    $user_count_row = $user_count_result->fetch_assoc();
    $user_count = $user_count_row['total_users'];
} else {
    $user_count = 0; // Default value in case of query failure
}

// Fetch total number of weekly visits
$weekly_visit_count = getWeeklyVisitCount($conn);

// Function to create the reviews table
function createReviewsTable($conn) {
    $create_table_query = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        rating INT,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
        -- You can add more FOREIGN KEY constraints as needed
    )";
    // if ($conn->query($create_table_query) === TRUE) {
    //     echo "Table reviews created successfully";
    // } else {
    //     echo "Error creating table: " . $conn->error;
    // }
}
// Call the function to create the reviews table
createReviewsTable($conn);
// Close the database connection (no)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/index/index.css">
    <link rel="stylesheet" href="./styles/index/quote.css">
    <link rel="stylesheet" href="./styles/index/programming-text.css">
    <link rel="stylesheet" href="./styles/index/modal.css">
    <link rel="stylesheet" href="./styles/index/satisfied-users.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <title>Landing Page</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Menu</a>
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

    <section class="mt-4">
        <div class="container-fluid">
            <div class="collapsible">
                <button id="prev"><img src="./img/index/previous.png" alt="previous"></button>
                <div class="content">
                    <div class="text-container slide active">
                        <div class="row">
                            <span class="gradient-text">Welcome to our landing page!</span>
                        </div>
                        <div class="row">
                            <div class="quote-container">
                                <p class="quote">"Alone we can do so little; together we can do so much."</p>
                                <p class="author">- Helen Keller</p>
                            </div>
                        </div>
                        <div class="row stats-row">
                            <div class="stat-item">
                                <span class="bold-text">Total Users:</span>
                                <p class="bold-text-number"><?php echo $user_count; ?></p>
                            </div>
                            <div class="stat-item">
                                <span class="bold-text">Time in Existence:</span>
                                <p id="time-in-existence" class="bold-text-number"></p>
                            </div>
                            <div class="stat-item">
                                <span class="bold-text">Weekly Visits:</span>
                                <p class="bold-text-number"><?php echo $weekly_visit_count; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="row">
                            <span class="gradient-text">Satisfied Users</span>
                        </div>
                        <div class="row">
                            <div id="satisfied-users" class="satisfied-users">
                                <!-- User ratings will be injected here -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="slide">
                        <div class="row">
                            <!-- Universal message display block -->
                            <?php if (!empty($_SESSION['message'])): ?> 
                                <div class="alert alert-<?php echo $_SESSION['alert_type']?>" role="allert">
                                    <?php echo $_SESSION['message']; ?>
                                </div>
                                <?php unset($_SESSION['message'], $_SESSION['alert_type']); // clear the message after displaying ?>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <span class="gradient-text">Rate Us</span>
                        </div>
                        <form id="rating-form">
                            <div class="row">
                                <div class="rating-system">
                                    <div class="stars">
                                        <span class="star" data-value="1">★</span>
                                        <span class="star" data-value="2">★</span>
                                        <span class="star" data-value="3">★</span>
                                        <span class="star" data-value="4">★</span>
                                        <span class="star" data-value="5">★</span>
                                    </div>
                                    <input type="hidden" id="user-rating" name="user-rating" value="0">
                                </div>
                                <textarea id="user-message" name="user-message" class="message-textarea"></textarea>
                            </div>
                            <div class="row">
                                <button type="submit" name="submit" class="submit-btn-rating">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <button id="next"><img src="./img/index/next.png" alt="next"></button>
            </div>
        </div>
    </section>


    <!-- Custom Message Modal -->
    <div id="messageModal" class="custom-modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">×</span>
            <div class="modal-body" id="messageContent">
                <!-- Message will be injected here -->
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('messageModal');
            var closeButton = document.querySelector('.close-button');

            <?php if (!empty($_SESSION['message'])): ?>
                var messageType = "<?php echo $_SESSION['alert_type']; ?>";
                var message = "<?php echo addslashes($_SESSION['message']); ?>";
                var messageContent = document.getElementById('messageContent');
                messageContent.innerHTML = message;

                // Set modal body background color based on message type
                if (messageType === 'success') {
                    messageContent.style.backgroundColor = '#ccffcc'; // light lime green
                } else if (messageType === 'danger') {
                    messageContent.style.backgroundColor = '#ffcccc'; // light pink
                }

                // Show the modal
                modal.style.display = 'block';

                <?php unset($_SESSION['message'], $_SESSION['alert_type']); ?>
            <?php endif; ?>

            closeButton.onclick = function() {
                modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });
    </script>


    <script src="./js/index/collapsible.js"></script>
    <script src="./js/index/since.js"></script>
    <script src="./js/index/rating-system.js"></script>
    <script src="./js/index/post.js"></script>
    <script src="./js/index/satisfied-users.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
