<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to add experience.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $position = trim($_POST['position']);
    $jobTitle = trim($_POST['job_title']);
    $companyName = trim($_POST['company_name']);
    $location = trim($_POST['location']);
    $startDate = $_POST['start_date'];
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $currentlyWorking = isset($_POST['currently_working']) ? 1 : 0;
    $description = trim($_POST['description']);

    // Validate required fields
    if (empty($position) || empty($jobTitle) || empty($companyName) || empty($location) || empty($startDate) || empty($description)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Ensure end date is null if "currently working" is checked
    if ($currentlyWorking) {
        $endDate = null;
    }

    // Insert experience details into the database
    $stmt = $conn->prepare("INSERT INTO experience (user_id, position, job_title, company_name, location, start_date, end_date, currently_working, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("issssssis", $userId, $position, $jobTitle, $companyName, $location, $startDate, $endDate, $currentlyWorking, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Experience added successfully!'); window.location.href='job-seeker-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to add experience. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='job-seeker-profile.php';</script>";
    exit();
}

$conn->close();
