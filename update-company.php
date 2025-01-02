<?php
session_start();
include 'config.php';

// Ensure the connection is active
if (!$conn || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyId = intval($_POST['id']);
    $companyName = trim($_POST['name']);
    $companyType = trim($_POST['type']);
    $companyEmail = trim($_POST['email']);
    $companyPhone = trim($_POST['phone']);
    $companyWebsite = trim($_POST['website']);
    $companyAddress = trim($_POST['address']);
    $companyDescription = trim($_POST['description']);

    // Validate required fields
    if (empty($companyName) || empty($companyType) || empty($companyEmail) || empty($companyPhone) || empty($companyAddress)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/company_logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $logoName = basename($_FILES['logo']['name']);
        $logoPath = $uploadDir . time() . "_" . $logoName;

        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
            echo "<script>alert('Failed to upload company logo.'); window.history.back();</script>";
            exit();
        }

        // Update the logo in the database
        $stmtLogo = $conn->prepare("UPDATE companies SET logo = ? WHERE id = ? AND added_by = ?");
        if (!$stmtLogo) {
            die("Prepare statement failed: " . $conn->error);
        }

        $userId = $_SESSION['user_id'];
        $stmtLogo->bind_param("sii", $logoPath, $companyId, $userId);
        if (!$stmtLogo->execute()) {
            echo "<script>alert('Failed to update company logo. Please try again.'); window.history.back();</script>";
            exit();
        }
        $stmtLogo->close();
    }

    // Update the company details in the database
    $stmt = $conn->prepare("UPDATE companies SET name = ?, type = ?, email = ?, phone = ?, website = ?, address = ?, description = ? WHERE id = ? AND added_by = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $userId = $_SESSION['user_id'];
    $stmt->bind_param("sssssssii", $companyName, $companyType, $companyEmail, $companyPhone, $companyWebsite, $companyAddress, $companyDescription, $companyId, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Company updated successfully!'); window.location.href='manage-companies.php';</script>";
    } else {
        echo "<script>alert('Failed to update company. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='manage-companies.php';</script>";
    exit();
}

$conn->close();
?>