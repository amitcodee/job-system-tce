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
    echo "<script>alert('Invalid job ID.'); window.location.href='all-joblist.php';</script>";
    exit();
}

$jobId = intval($_GET['id']);

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

// Delete the job
$deleteStmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
$deleteStmt->bind_param("i", $jobId);

if ($deleteStmt->execute()) {
    echo "<script>alert('Job deleted successfully.'); window.location.href='all-joblist.php';</script>";
} else {
    echo "<script>alert('Failed to delete the job. Please try again.'); window.history.back();</script>";
}

$deleteStmt->close();
$conn->close();
?>
