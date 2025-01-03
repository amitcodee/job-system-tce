<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to create a resume.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $education = trim($_POST['education']);
    $experience = trim($_POST['experience']);
    $skills = trim($_POST['skills']);
    $profilePicturePath = null;

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profile_pictures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['profile_picture']['name']);
        $profilePicturePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profilePicturePath);
    }

    // Check if a resume already exists for the user
    $stmt = $conn->prepare("SELECT id FROM resumes WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing resume
        $stmt = $conn->prepare("UPDATE resumes SET name = ?, email = ?, phone = ?, address = ?, education = ?, experience = ?, skills = ?, profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("ssssssssi", $name, $email, $phone, $address, $education, $experience, $skills, $profilePicturePath, $userId);
    } else {
        // Insert new resume
        $stmt = $conn->prepare("INSERT INTO resumes (user_id, name, email, phone, address, education, experience, skills, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssss", $userId, $name, $email, $phone, $address, $education, $experience, $skills, $profilePicturePath);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Resume saved successfully!'); window.location.href='resume.php';</script>";
    } else {
        echo "<script>alert('Failed to save resume. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='resume.php';</script>";
    exit();
}

$conn->close();
?>
