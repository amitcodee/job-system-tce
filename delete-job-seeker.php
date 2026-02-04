<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Validate ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid user ID.'); window.location.href='all-job-seekers.php';</script>";
    exit();
}

$targetId = (int) $_GET['id'];

// Fetch the user's role
$userId = $_SESSION['user_id'];
$roleStmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$roleStmt->bind_param("i", $userId);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
if ($roleResult->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit();
}
$userRole = $roleResult->fetch_assoc()['role'];
$roleStmt->close();

// Restrict access to admin only
if ($userRole !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Delete the job seeker
$deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role IN ('job_seeker','deactive')");
$deleteStmt->bind_param("i", $targetId);

if ($deleteStmt->execute()) {
    echo "<script>alert('User deleted successfully.'); window.location.href='all-job-seekers.php';</script>";
} else {
    echo "<script>alert('Failed to delete the user. Please try again.'); window.history.back();</script>";
}

$deleteStmt->close();
$conn->close();
?>
