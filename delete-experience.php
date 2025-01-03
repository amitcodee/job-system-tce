<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to perform this action.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the experience ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $experienceId = intval($_POST['id']);
    $userId = $_SESSION['user_id'];

    // Delete the experience record
    $stmt = $conn->prepare("DELETE FROM experience WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $experienceId, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Experience record deleted successfully!'); window.location.href='job-seeker-profile.php';</script>";
    } else {
        echo "<script>alert('Failed to delete experience record. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}

$conn->close();
?>
