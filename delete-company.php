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

// Check if the id parameter is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $companyId = intval($_GET['id']);

    // Verify that the company belongs to the logged-in user
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, logo FROM companies WHERE id = ? AND added_by = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $companyId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $company = $result->fetch_assoc();

        // Delete the company record
        $deleteStmt = $conn->prepare("DELETE FROM companies WHERE id = ? AND added_by = ?");
        if (!$deleteStmt) {
            die("Prepare statement failed: " . $conn->error);
        }

        $deleteStmt->bind_param("ii", $companyId, $userId);
        if ($deleteStmt->execute()) {
            // Delete the logo file if it exists
            if (!empty($company['logo']) && file_exists($company['logo'])) {
                unlink($company['logo']);
            }

            echo "<script>alert('Company deleted successfully!'); window.location.href='manage-companies.php';</script>";
        } else {
            echo "<script>alert('Failed to delete company. Please try again.'); window.history.back();</script>";
        }

        $deleteStmt->close();
    } else {
        echo "<script>alert('Company not found or unauthorized access.'); window.location.href='manage-companies.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='manage-companies.php';</script>";
    exit();
}

$conn->close();
?>
