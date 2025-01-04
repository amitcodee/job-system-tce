<?php
session_start();
include 'config.php'; // Adjust the path as per your file structure

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to perform this action.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $position = trim($_POST['position']);
    $companyName = trim($_POST['company_name']);
    $description = trim($_POST['description'] ?? '');
    $joinLetterFileName = null;

    // Validate required fields
    if (empty($position) || empty($companyName)) {
        echo "<script>alert('Position and Company Name are required.'); window.history.back();</script>";
        exit();
    }

    // Handle file upload for the joining letter
    if (isset($_FILES['join_letter']) && $_FILES['join_letter']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileTmpPath = $_FILES['join_letter']['tmp_name'];
        $fileName = $_FILES['join_letter']['name'];
        $fileSize = $_FILES['join_letter']['size'];
        $fileType = $_FILES['join_letter']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Only PDF, DOC, or DOCX files are allowed for joining letters.'); window.history.back();</script>";
            exit();
        }

        if ($fileSize > 5 * 1024 * 1024) { // Limit file size to 5MB
            echo "<script>alert('The joining letter file size must be less than 5MB.'); window.history.back();</script>";
            exit();
        }

        // Define the upload directory
        $uploadDir = 'uploads/joining_letters/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $joinLetterFileName = $userId . '_joining_letter_' . time() . '.' . $fileExtension;
        $uploadFilePath = $uploadDir . $joinLetterFileName;

        if (!move_uploaded_file($fileTmpPath, $uploadFilePath)) {
            echo "<script>alert('Failed to upload the joining letter. Please try again.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Joining Letter is required.'); window.history.back();</script>";
        exit();
    }

    // Insert placement data into the database
    $stmt = $conn->prepare("
        INSERT INTO placements (user_id, position, company_name, description, join_letter) 
        VALUES (?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare the statement. Please try again.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("issss", $userId, $position, $companyName, $description, $joinLetterFileName);

    if ($stmt->execute()) {
        echo "<script>alert('Placement details saved successfully!'); window.location.href='placement-data.php';</script>";
    } else {
        echo "<script>alert('Failed to save placement details. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='placement-data.php';</script>";
    exit();
}

$conn->close();
