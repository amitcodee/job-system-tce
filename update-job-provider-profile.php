<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to update your profile.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $position = trim($_POST['position']);
    $profileDescription = trim($_POST['profile_description']);
    $companyId = isset($_POST['company_id']) && is_numeric($_POST['company_id']) ? intval($_POST['company_id']) : null;

    // Validate required fields
    if (empty($position)) {
        echo "<script>alert('Position is required.'); window.history.back();</script>";
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Update the user's details in the database
        $query = "UPDATE users SET position = ?, profile_description = ? WHERE id = ? AND role = 'job_provider'";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare the statement.");
        }

        $stmt->bind_param("ssi", $position, $profileDescription, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update your profile.");
        }
        $stmt->close();

        // Update the company association if provided
        if ($companyId) {
            $companyQuery = "UPDATE companies SET added_by = ? WHERE id = ?";
            $companyStmt = $conn->prepare($companyQuery);
            if (!$companyStmt) {
                throw new Exception("Failed to prepare the company update statement.");
            }

            $companyStmt->bind_param("ii", $userId, $companyId);
            if (!$companyStmt->execute()) {
                throw new Exception("Failed to update the associated company.");
            }
            $companyStmt->close();
        }

        // Commit transaction
        $conn->commit();
        echo "<script>alert('Profile updated successfully!'); window.location.href='job-provider-profile.php';</script>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>alert('{$e->getMessage()}'); window.history.back();</script>";
    }
    exit();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='job-provider-profile.php';</script>";
    exit();
}

$conn->close();
?>
