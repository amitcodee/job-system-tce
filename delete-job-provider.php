<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch the user's role from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Validate the user's role
if (!$user || $user['role'] !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Validate ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid provider ID.'); window.location.href='all-job-providers.php';</script>";
    exit();
}

$providerId = intval($_GET['id']);

// Delete the job provider from the database
$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'job_provider'");
$stmt->bind_param("i", $providerId);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "<script>alert('Job provider deleted successfully!'); window.location.href='all-job-providers.php';</script>";
} else {
    echo "<script>alert('Failed to delete job provider or provider not found.'); window.location.href='all-job-providers.php';</script>";
}
$stmt->close();
$conn->close();
?>
