<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request method.'); window.location.href='all-job-seekers.php';</script>";
    exit();
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
$targetUserId = (int) ($_POST['user_id'] ?? 0);
$companyName = trim($_POST['company_name'] ?? '');
$profile = trim($_POST['profile'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

if ($targetUserId <= 0 || $companyName === '' || $profile === '' || $remarks === '') {
    echo "<script>alert('Please fill all fields.'); window.history.back();</script>";
    exit();
}

// Fetch the user's role
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

// Insert placement
$insertStmt = $conn->prepare("INSERT INTO placements (user_id, company_name, profile, remarks) VALUES (?, ?, ?, ?)");
$insertStmt->bind_param("isss", $targetUserId, $companyName, $profile, $remarks);

if ($insertStmt->execute()) {
    echo "<script>alert('Placement added successfully.'); window.location.href='all-job-seekers.php';</script>";
} else {
    echo "<script>alert('Failed to add placement. Please try again.'); window.history.back();</script>";
}

$insertStmt->close();
$conn->close();
?>
