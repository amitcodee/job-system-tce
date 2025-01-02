<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to post a job.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobTitle = trim($_POST['job_title']);
    $jobType = trim($_POST['job_type']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $category = trim($_POST['category']);
    $companyId = intval($_POST['company_id']);
    $description = trim($_POST['description']);

    // Validate required fields
    if (empty($jobTitle) || empty($jobType) || empty($location) || empty($category) || empty($companyId) || empty($description)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Insert job into the database
    $stmt = $conn->prepare("INSERT INTO jobs (title, type, location, salary, category, company_id, description, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $userId = $_SESSION['user_id'];
    $stmt->bind_param("sssssssi", $jobTitle, $jobType, $location, $salary, $category, $companyId, $description, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Job posted successfully!'); window.location.href='manage-jobs.php';</script>";
    } else {
        echo "<script>alert('Failed to post the job. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='post-job.php';</script>";
    exit();
}

$conn->close();
?>
