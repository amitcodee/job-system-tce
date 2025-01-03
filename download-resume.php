<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to download the resume.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch resume data for the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM resumes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();

if (!$resume) {
    echo "<script>alert('No resume found for the current user.'); window.history.back();</script>";
    exit();
}

// Set headers for PDF file download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Resume.pdf"');

// Start generating PDF content
echo "%PDF-1.4\n";
echo "1 0 obj\n";
echo "<< /Type /Catalog /Pages 2 0 R >>\n";
echo "endobj\n";
echo "2 0 obj\n";
echo "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";
echo "endobj\n";
echo "3 0 obj\n";
echo "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << >> >>\n";
echo "endobj\n";
echo "4 0 obj\n";
echo "<< /Length 5 0 R >>\n";
echo "stream\n";
echo "BT /F1 24 Tf 100 700 Td (Resume) Tj ET\n";
echo "BT /F1 12 Tf 100 650 Td (Name: " . $resume['name'] . ") Tj ET\n";
echo "BT /F1 12 Tf 100 630 Td (Email: " . $resume['email'] . ") Tj ET\n";
echo "BT /F1 12 Tf 100 610 Td (Phone: " . $resume['phone'] . ") Tj ET\n";
echo "BT /F1 12 Tf 100 590 Td (Address: " . $resume['address'] . ") Tj ET\n";
echo "BT /F1 12 Tf 100 570 Td (Educational Background: " . $resume['education'] . ") Tj ET\n";
echo "BT /F1 12 Tf 100 550 Td (Experience: " . $resume['experience'] . ") Tj ET\n";
echo "BT /F1 12 Tf 100 530 Td (Skills: " . $resume['skills'] . ") Tj ET\n";
echo "endstream\n";
echo "endobj\n";
echo "5 0 obj\n";
echo "1024\n";
echo "endobj\n";
echo "trailer\n";
echo "<< /Root 1 0 R >>\n";
echo "%%EOF\n";

exit();
?>
