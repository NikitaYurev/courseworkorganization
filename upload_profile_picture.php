<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_picture'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) {
                $fileNameNew = "profile" . $user_id . "." . $fileActualExt;
                $fileDestination = 'uploads/' . $fileNameNew;

                // Resize the image
                $targetWidth = 200; // Desired width
                $targetHeight = 200; // Desired height

                if ($fileActualExt == 'jpg' || $fileActualExt == 'jpeg') {
                    $src = imagecreatefromjpeg($fileTmpName);
                } else if ($fileActualExt == 'png') {
                    $src = imagecreatefrompng($fileTmpName);
                }

                list($width, $height) = getimagesize($fileTmpName);
                $tmp = imagecreatetruecolor($targetWidth, $targetHeight);
                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

                if ($fileActualExt == 'jpg' || $fileActualExt == 'jpeg') {
                    imagejpeg($tmp, $fileDestination, 100);
                } else if ($fileActualExt == 'png') {
                    imagepng($tmp, $fileDestination, 9);
                }

                imagedestroy($src);
                imagedestroy($tmp);

                // Update profile picture path in database
                $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('si', $fileDestination, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'Profile picture updated successfully!';
                    $_SESSION['alert_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Error updating profile picture in database!';
                    $_SESSION['alert_type'] = 'danger';
                }
                header('Location: profile.php');
                exit;
            } else {
                $_SESSION['message'] = 'Your file is too big!';
                $_SESSION['alert_type'] = 'danger';
                header('Location: profile.php');
                exit;
            }
        } else {
            $_SESSION['message'] = 'There was an error uploading your file!';
            $_SESSION['alert_type'] = 'danger';
            header('Location: profile.php');
            exit;
        }
    } else {
        $_SESSION['message'] = 'You cannot upload files of this type!';
        $_SESSION['alert_type'] = 'danger';
        header('Location: profile.php');
        exit;
    }
} else {
    $_SESSION['message'] = 'No file was uploaded!';
    $_SESSION['alert_type'] = 'danger';
    header('Location: profile.php');
    exit;
}
?>
