<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the job ID is provided
if (isset($_GET['id'])) {
    $jobId = intval($_GET['id']);

    // Validate if the job belongs to the logged-in user
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ? AND posted_by = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $jobId, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Job deleted successfully!'); window.location.href='manage-jobs.php';</script>";
    } else {
        echo "<script>alert('Failed to delete the job. Please try again.'); window.location.href='manage-jobs.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid job ID.'); window.location.href='manage-jobs.php';</script>";
    exit();
}

$conn->close();
?>
