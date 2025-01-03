<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to delete a message.'); window.location.href='login.php';</script>";
    exit();
}

// Validate ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid message ID.'); window.location.href='all_message_query.php';</script>";
    exit();
}

$messageId = intval($_GET['id']);

// Delete the message
$stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
$stmt->bind_param("i", $messageId);

if ($stmt->execute()) {
    echo "<script>alert('Message deleted successfully.'); window.location.href='all_message_query.php';</script>";
} else {
    echo "<script>alert('Failed to delete the message. Please try again.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
