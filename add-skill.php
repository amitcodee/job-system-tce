<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to add a skill.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $skillName = trim($_POST['skill_name']);
    $proficiencyLevel = trim($_POST['proficiency_level']);

    // Validate required fields
    if (empty($skillName) || empty($proficiencyLevel)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Insert the skill into the database
    $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, proficiency_level, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("iss", $userId, $skillName, $proficiencyLevel);

    if ($stmt->execute()) {
        echo "<script>alert('Skill added successfully!'); window.location.href='job-seeker-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to add the skill. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='job-seeker-profile.php';</script>";
    exit();
}

$conn->close();
