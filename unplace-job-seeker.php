<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);

    // Remove placement record
    $stmt = $conn->prepare("DELETE FROM placements WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            echo "<script>alert('Placement record removed successfully.'); window.location.href='placement-data.php';</script>";
        } else {
            echo "<script>alert('Failed to remove placement record.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Failed to prepare statement.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='placement-data.php';</script>";
}

$conn->close();
?>
