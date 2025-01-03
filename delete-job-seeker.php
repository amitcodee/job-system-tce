<?php
session_start();
include 'config.php';

// Check if the user is logged in and has admin rights
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to perform this action.'); window.location.href='login.php';</script>";
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'admin') {
    echo "<script>alert('You do not have permission to perform this action.'); window.location.href='all-job-seekers.php';</script>";
    exit();
}

// Check if the ID parameter is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $seekerId = intval($_GET['id']);

    // Delete the job seeker
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'job_seeker'");
    $stmt->bind_param("i", $seekerId);

    if ($stmt->execute()) {
        echo "<script>alert('Job seeker deleted successfully.'); window.location.href='all-job-seekers.php';</script>";
    } else {
        echo "<script>alert('Failed to delete the job seeker. Please try again.'); window.location.href='all-job-seekers.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='all-job-seekers.php';</script>";
}

$conn->close();
?>
