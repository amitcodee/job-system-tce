<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to delete a document.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the ID is provided in the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid document ID.'); window.location.href='document-collection.php';</script>";
    exit();
}

$documentId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

// Fetch the document to verify ownership and get the file path
$stmt = $conn->prepare("SELECT file_path FROM user_documents WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $documentId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
    $filePath = $document['file_path'];

    // Delete the document record from the database
    $stmt = $conn->prepare("DELETE FROM user_documents WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $documentId, $userId);

    if ($stmt->execute()) {
        // Remove the file from the server
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        echo "<script>alert('Document deleted successfully!'); window.location.href='document-collection.php';</script>";
    } else {
        echo "<script>alert('Failed to delete the document. Please try again.'); window.location.href='document-collection.php';</script>";
    }
} else {
    echo "<script>alert('Document not found or unauthorized access.'); window.location.href='document-collection.php';</script>";
}

$stmt->close();
$conn->close();
?>
