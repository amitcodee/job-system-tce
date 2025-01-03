<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to update your profile.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    // Update admin details in the database
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ? AND role = 'admin'");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("sssi", $name, $email, $phone, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='admin-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='admin-profile.php';</script>";
    exit();
}

$conn->close();
?>
