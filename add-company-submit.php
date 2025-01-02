<?php
session_start();
include 'config.php'; // Include database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch the user's role from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $role = $user['role'];
    if ($role !== 'job_provider') {
        echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit();
}

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = trim($_POST['company_name']);
    $companyType = trim($_POST['company_type']);
    $companyWebsite = trim($_POST['company_website']);
    $companyEmail = trim($_POST['company_email']);
    $companyPhone = trim($_POST['company_phone']);
    $companyAddress = trim($_POST['company_address']);
    $companyDescription = trim($_POST['company_description']);

    // Handle logo upload
    $logoPath = null;
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/company_logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $logoName = basename($_FILES['company_logo']['name']);
        $logoPath = $uploadDir . time() . "_" . $logoName;

        if (!move_uploaded_file($_FILES['company_logo']['tmp_name'], $logoPath)) {
            echo "<script>alert('Failed to upload company logo.'); window.location.href='add-company.php';</script>";
            exit();
        }
    }

    // Validate required fields
    if (empty($companyName) || empty($companyType) || empty($companyEmail) || empty($companyPhone) || empty($companyAddress)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href='add-company.php';</script>";
        exit();
    }

    // Check if the company already exists for the recruiter
    $stmt = $conn->prepare("SELECT id FROM companies WHERE name = ? AND added_by = ?");
    $stmt->bind_param("si", $companyName, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('A company with the same name already exists under your account.'); window.location.href='add-company.php';</script>";
        exit();
    }

    $stmt->close();

    // Insert company details into the database
    $stmt = $conn->prepare("INSERT INTO companies (name, type, website, email, phone, address, description, logo, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $companyName, $companyType, $companyWebsite, $companyEmail, $companyPhone, $companyAddress, $companyDescription, $logoPath, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo "<script>alert('Company added successfully!'); window.location.href='add-company.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error adding company. Please try again.'); window.location.href='add-company.php';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='add-company.php';</script>";
    exit();
}
?>
