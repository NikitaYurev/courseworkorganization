<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, role, bio, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 'true';
$profilePicture = $user['profile_picture'] ? $user['profile_picture'] : 'img/profile/default.jpg';

// Fetch review if it exists
$review_query = "SELECT comment, rating FROM reviews WHERE user_id = ?";
$review_stmt = $conn->prepare($review_query);
$review_stmt->bind_param('i', $user_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result();
$review = $review_result->fetch_assoc();
$existing_review = $review ? $review['comment'] : ''; // Set an empty string if no review found
$existing_rating = $review ? $review['rating'] : 0; // Set rating to 0 if no review found
$button_text = $existing_review ? "Update Review" : "Post Review";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/profile/profile.css">
    <link rel="stylesheet" href="styles/profile/modal.css">
    <title>Profile</title>
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Logged in as <?= htmlspecialchars($_SESSION['username']) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Profile</h2>
        <div class="row">
            <div class="col-md-4">
                <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="img-fluid">
                <?php if ($edit_mode): ?>
                    <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_picture" id="profile_picture" style="display: none;">
                        <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('profile_picture').click();">Change Avatar</button>
                        <button type="submit" class="btn btn-success mt-2">Upload</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <?php if ($edit_mode): ?>
                    <form action="update_profile.php" method="POST">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" name="role" id="role" class="form-control" value="<?= htmlspecialchars($user['role']) ?>" readonly>
                        </div>
                        <?php if ($user['role'] === 'coworker'): ?>
                            <div class="form-group">
                                <label for="working_days">Working Days</label>
                                <input type="text" name="working_days" id="working_days" class="form-control" value="Mo, Tu, We, Th, Fr" readonly>
                            </div>
                            <div class="form-group">
                                <label for="working_hours">Working Hours</label>
                                <input type="text" name="working_hours" id="working_hours" class="form-control" value="9 AM - 5 PM" readonly>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea name="bio" id="bio" class="form-control"><?= htmlspecialchars($user['bio']) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="profile.php" class="btn btn-secondary">Exit Edit Mode</a>
                    </form>
                <?php else: ?>
                    <div>
                        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                        <?php if ($user['role'] === 'coworker'): ?>
                            <p><strong>Working Days:</strong> Mo, Tu, We, Th, Fr</p>
                            <p><strong>Working Hours:</strong> 9 AM - 5 PM</p>
                        <?php endif; ?>
                        <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                        <a href="?edit=true" class="btn btn-primary">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <h2>Leave a Review</h2>
        <form id="review-form" data-update="<?= !empty($existing_review) ? '1' : '0'?>">
            <input type="hidden" id="is_update" name="is_update" value="<?= !empty($existing_review) ? '1' : '0'?>">
            <div class="rating-system">
                <span class="star" data-value="1">★</span>
                <span class="star" data-value="2">★</span>
                <span class="star" data-value="3">★</span>
                <span class="star" data-value="4">★</span>
                <span class="star" data-value="5">★</span>
            </div>
            <div class="form-group">
                <textarea id="review-text" name="review-text" class="form-control" rows="6" placeholder="Enter your review here..."><?= htmlspecialchars($existing_review) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= $button_text ?></button>
        </form>
    </div>

    <!-- Custom Message Modal -->
    <div id="messageModal" class="custom-modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">×</span>
            <div class="modal-body" id="messageContent">
                <!-- Message will be injected here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="./js/profile/profile.js"></script>
    <script src="./js/profile/ajax.js"></script>
</body>
</html>
