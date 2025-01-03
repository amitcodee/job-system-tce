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

// Fetch the current verification status
$stmt = $conn->prepare("SELECT verified FROM users WHERE id = ? AND role = 'job_provider'");
$stmt->bind_param("i", $providerId);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$provider) {
    echo "<script>alert('Job provider not found.'); window.location.href='all-job-providers.php';</script>";
    exit();
}

// Toggle the verification status
$newStatus = $provider['verified'] ? 0 : 1;
$updateStmt = $conn->prepare("UPDATE users SET verified = ? WHERE id = ?");
$updateStmt->bind_param("ii", $newStatus, $providerId);
if ($updateStmt->execute()) {
    echo "<script>alert('Verification status updated successfully!'); window.location.href='all-job-providers.php';</script>";
} else {
    echo "<script>alert('Failed to update verification status. Please try again.'); window.history.back();</script>";
}
$updateStmt->close();
$conn->close();
?>
