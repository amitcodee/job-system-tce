<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to perform this action.'); window.location.href='login.php';</script>";
    exit();
}

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid job preference ID.'); window.location.href='jobs.php';</script>";
    exit();
}

// Sanitize and validate the job preference ID
$jobPreferenceId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

// Check if the job preference belongs to the logged-in user
$stmt = $conn->prepare("SELECT id FROM job_preferences WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $jobPreferenceId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Job preference not found or you do not have permission to delete it.'); window.location.href='jobs.php';</script>";
    exit();
}

// Delete the job preference
$stmt = $conn->prepare("DELETE FROM job_preferences WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $jobPreferenceId, $userId);

if ($stmt->execute()) {
    echo "<script>alert('Job preference deleted successfully.'); window.location.href='jobs.php';</script>";
} else {
    echo "<script>alert('Failed to delete job preference. Please try again.'); window.location.href='jobs.php';</script>";
}

$stmt->close();
$conn->close();
?>
