<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = intval($_POST['id']);
    $jobTitle = trim($_POST['job_title']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $description = trim($_POST['description']);

    // Validate required fields
    if (empty($jobTitle) || empty($location) || empty($description)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Update the job in the database
    $stmt = $conn->prepare("
        UPDATE jobs
        SET title = ?, location = ?, salary = ?, description = ?
        WHERE id = ? AND posted_by = ?
    ");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $userId = $_SESSION['user_id'];
    $stmt->bind_param("ssssii", $jobTitle, $location, $salary, $description, $jobId, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Job updated successfully!'); window.location.href='manage-jobs.php';</script>";
    } else {
        echo "<script>alert('Failed to update the job. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='manage-jobs.php';</script>";
    exit();
}

$conn->close();
?>
