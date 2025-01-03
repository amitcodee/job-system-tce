<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to add education.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $degree = trim($_POST['degree']);
    $institution = trim($_POST['institution']);
    $fieldOfStudy = trim($_POST['field_of_study']);
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $description = trim($_POST['description']);

    // Validate required fields
    if (empty($degree) || empty($institution) || empty($fieldOfStudy) || empty($startDate) || empty($description)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Insert education details into the database
    $stmt = $conn->prepare("INSERT INTO education (user_id, degree, institution, field_of_study, start_date, end_date, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("issssss", $userId, $degree, $institution, $fieldOfStudy, $startDate, $endDate, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Education added successfully!'); window.location.href='job-seeker-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to add education. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='job-seeker-profile.php';</script>";
    exit();
}

$conn->close();
