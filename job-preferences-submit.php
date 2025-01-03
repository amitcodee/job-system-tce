<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to submit preferences.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $jobTitle = trim($_POST['job_title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $salaryExpectation = trim($_POST['salary_expectation']);
    $additionalNotes = trim($_POST['additional_notes']);

    // Validate required fields
    if (empty($jobTitle) || empty($category) || empty($location)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Check if the user already has 4 preferences
    $stmt = $conn->prepare("SELECT COUNT(*) AS preference_count FROM job_preferences WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['preference_count'] >= 4) {
        echo "<script>alert('You can only create up to 4 job preferences.'); window.location.href='jobs.php';</script>";
        exit();
    }

    // Insert new preference
    $stmt = $conn->prepare("INSERT INTO job_preferences (user_id, job_title, category, location, salary_expectation, additional_notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $userId, $jobTitle, $category, $location, $salaryExpectation, $additionalNotes);

    if ($stmt->execute()) {
        echo "<script>alert('Job preference saved successfully.'); window.location.href='jobs.php';</script>";
    } else {
        echo "<script>alert('Failed to save job preference. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='jobs.php';</script>";
    exit();
}
?>
