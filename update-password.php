<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to update your password.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $newPassword = trim($_POST['new_password']);

    // Validate the password
    if (empty($newPassword) || strlen($newPassword) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.'); window.history.back();</script>";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("si", $hashedPassword, $userId);
    if ($stmt->execute()) {
        echo "<script>alert('Password updated successfully!'); window.location.href='account-settings.php';</script>";
    } else {
        echo "<script>alert('Failed to update password. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='account-settings.php';</script>";
    exit();
}

$conn->close();
?>
