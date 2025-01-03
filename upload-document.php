<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to upload a document.'); window.location.href='login.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documentName = trim($_POST['document_name']);
    $userId = $_SESSION['user_id'];
    $uploadDir = 'uploads/documents/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file upload
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document_file']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['document_file']['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            // Insert document record into the database
            $stmt = $conn->prepare("INSERT INTO user_documents (user_id, name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $documentName, $filePath);

            if ($stmt->execute()) {
                echo "<script>alert('Document uploaded successfully!'); window.location.href='document-collection.php';</script>";
            } else {
                echo "<script>alert('Failed to save document. Please try again.'); window.location.href='document-collection.php';</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload file. Please try again.'); window.location.href='document-collection.php';</script>";
        }
    } else {
        echo "<script>alert('Please choose a valid file.'); window.location.href='document-collection.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='document-collection.php';</script>";
}

$conn->close();
?>
