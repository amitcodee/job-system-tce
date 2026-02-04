<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to apply.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request.'); window.location.href='jobshow.php';</script>";
    exit();
}

$jobId = isset($_POST['job_id']) ? (int) $_POST['job_id'] : 0;
if ($jobId <= 0) {
    echo "<script>alert('Invalid job.'); window.location.href='jobshow.php';</script>";
    exit();
}

$userId = (int) $_SESSION['user_id'];

// Ensure profile is complete
$profileStmt = $conn->prepare("SELECT name, dob, father_name, mother_name, address, languages_known, profile_summary, resume, resume_path FROM users WHERE id = ?");
$profileStmt->bind_param("i", $userId);
$profileStmt->execute();
$user = $profileStmt->get_result()->fetch_assoc();
$profileStmt->close();

$missingFields = [];
if (empty(trim($user['name'] ?? ''))) { $missingFields[] = 'Full Name'; }
if (empty(trim($user['dob'] ?? ''))) { $missingFields[] = 'Date of Birth'; }
if (empty(trim($user['father_name'] ?? ''))) { $missingFields[] = "Father's Name"; }
if (empty(trim($user['mother_name'] ?? ''))) { $missingFields[] = "Mother's Name"; }
if (empty(trim($user['address'] ?? ''))) { $missingFields[] = 'Address'; }
if (empty(trim($user['languages_known'] ?? ''))) { $missingFields[] = 'Languages Known'; }
if (empty(trim($user['profile_summary'] ?? ''))) { $missingFields[] = 'Profile Summary'; }
if (empty(trim($user['resume'] ?? '')) && empty(trim($user['resume_path'] ?? ''))) { $missingFields[] = 'Resume'; }

if (!empty($missingFields)) {
    $fieldsText = implode(', ', $missingFields);
    echo "<script>alert('Please complete your profile before applying. Missing: {$fieldsText}.'); window.location.href='job-seeker-profile.php';</script>";
    exit();
}

// Ensure job exists
$jobStmt = $conn->prepare("SELECT id FROM jobs WHERE id = ?");
$jobStmt->bind_param("i", $jobId);
$jobStmt->execute();
$jobExists = $jobStmt->get_result()->num_rows > 0;
$jobStmt->close();

if (!$jobExists) {
    echo "<script>alert('Job not found.'); window.location.href='jobshow.php';</script>";
    exit();
}

// Check duplicate application
$checkStmt = $conn->prepare("SELECT id FROM job_applications WHERE job_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $jobId, $userId);
$checkStmt->execute();
$alreadyApplied = $checkStmt->get_result()->num_rows > 0;
$checkStmt->close();

if ($alreadyApplied) {
    echo "<script>alert('You have already applied for this job.'); window.location.href='jobshow.php';</script>";
    exit();
}

$insertStmt = $conn->prepare("INSERT INTO job_applications (job_id, user_id) VALUES (?, ?)");
$insertStmt->bind_param("ii", $jobId, $userId);
if ($insertStmt->execute()) {
    echo "<script>alert('Application submitted successfully.'); window.location.href='jobshow.php';</script>";
} else {
    echo "<script>alert('Failed to submit application. Please try again.'); window.location.href='jobshow.php';</script>";
}
$insertStmt->close();
$conn->close();
