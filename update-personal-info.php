<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to update personal information.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $dob = trim($_POST['dob']);
    $fatherName = trim($_POST['father_name']);
    $motherName = trim($_POST['mother_name']);
    $address = trim($_POST['address']);
    $languagesKnown = trim($_POST['languages_known']);
    $profileSummary = trim($_POST['profile_summary']);
    $resumeFileName = null;
    $resumeFilePath = null;

    // Validate required fields
    if (empty($name) || empty($dob) || empty($address)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Handle resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['pdf'];
        $fileTmpPath = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileSize = $_FILES['resume']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Only PDF files are allowed for resumes.'); window.history.back();</script>";
            exit();
        }

        if ($fileSize > 5 * 1024 * 1024) { // Limit file size to 5MB
            echo "<script>alert('Resume file size must be less than 5MB.'); window.history.back();</script>";
            exit();
        }

        // Define the upload directory
        $uploadDir = 'uploads/resumes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $resumeFileName = $userId . '_resume_' . time() . '.' . $fileExtension;
        $resumeFilePath = $uploadDir . $resumeFileName;

        if (!move_uploaded_file($fileTmpPath, $resumeFilePath)) {
            echo "<script>alert('Failed to upload the resume. Please try again.'); window.history.back();</script>";
            exit();
        }

        // Fetch the current resume and path for deletion
        $currentResumeStmt = $conn->prepare("SELECT resume, resume_path FROM users WHERE id = ?");
        $currentResumeStmt->bind_param("i", $userId);
        $currentResumeStmt->execute();
        $currentResume = $currentResumeStmt->get_result()->fetch_assoc();
        $currentResumeStmt->close();

        if (!empty($currentResume['resume_path']) && file_exists($currentResume['resume_path'])) {
            unlink($currentResume['resume_path']);
        }
    }

    // Update user details in the database
    $query = "
        UPDATE users 
        SET name = ?, dob = ?, father_name = ?, mother_name = ?, address = ?, languages_known = ?, profile_summary = ?
    ";
    if ($resumeFileName && $resumeFilePath) {
        $query .= ", resume = ?, resume_path = ?";
    }
    $query .= " WHERE id = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    if ($resumeFileName && $resumeFilePath) {
        $stmt->bind_param("ssssssssii", $name, $dob, $fatherName, $motherName, $address, $languagesKnown, $profileSummary, $resumeFileName, $resumeFilePath, $userId);
    } else {
        $stmt->bind_param("sssssssi", $name, $dob, $fatherName, $motherName, $address, $languagesKnown, $profileSummary, $userId);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Personal information updated successfully!'); window.location.href='job-seeker-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update personal information. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='job-seeker-profile.php';</script>";
    exit();
}

$conn->close();
?>
