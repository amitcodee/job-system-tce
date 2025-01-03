<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to add a certification.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $certificateName = trim($_POST['certificate_name']);
    $issuingOrganization = trim($_POST['issuing_organization']);
    $issueDate = $_POST['issue_date'];
    $credentialId = trim($_POST['credential_id'] ?? '');
    $credentialUrl = trim($_POST['credential_url'] ?? '');

    // Validate required fields
    if (empty($certificateName) || empty($issuingOrganization) || empty($issueDate)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Insert the certification details into the database
    $stmt = $conn->prepare("INSERT INTO certifications (user_id, name, issuing_organization, issue_date, credential_id, credential_url, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("isssss", $userId, $certificateName, $issuingOrganization, $issueDate, $credentialId, $credentialUrl);

    if ($stmt->execute()) {
        echo "<script>alert('Certification added successfully!'); window.location.href='job-seeker-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to add certification. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='job-seeker-profile.php';</script>";
    exit();
}

$conn->close();
?>
