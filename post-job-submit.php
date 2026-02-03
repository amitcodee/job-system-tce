<?php
session_start();
require_once 'php-mailer/PHPMailer.php';
require_once 'php-mailer/SMTP.php';
require_once 'php-mailer/Exception.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to post a job.'); window.location.href='login.php';</script>";
    exit();
}

// Allow only admin to post jobs
$userId = $_SESSION['user_id'];
$roleStmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$roleStmt->bind_param("i", $userId);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$userRole = $roleResult->fetch_assoc()['role'] ?? null;
$roleStmt->close();

if ($userRole !== 'admin') {
    echo "<script>alert('You do not have permission to post jobs.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobTitle = trim($_POST['job_title']);
    $jobType = trim($_POST['job_type']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $category = trim($_POST['category']);
    $companyName = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
    $companyWebsite = isset($_POST['company_website']) ? trim($_POST['company_website']) : '';
    $description = trim($_POST['description']);

    $jobTypeManual = isset($_POST['job_type_manual']) ? trim($_POST['job_type_manual']) : '';
    $categoryManual = isset($_POST['category_manual']) ? trim($_POST['category_manual']) : '';

    if ($jobType === 'Other' && $jobTypeManual !== '') {
        $jobType = $jobTypeManual;
    }

    if ($category === 'Other' && $categoryManual !== '') {
        $category = $categoryManual;
    }

    // Validate required fields
    if (empty($jobTitle) || empty($jobType) || empty($location) || empty($category) || empty($description) || empty($companyName)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Insert job into the database
    $stmt = $conn->prepare("INSERT INTO jobs (title, type, location, salary, category, company_name, company_website, description, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $userId = $_SESSION['user_id'];
    $stmt->bind_param("ssssssssi", $jobTitle, $jobType, $location, $salary, $category, $companyName, $companyWebsite, $description, $userId);

    if ($stmt->execute()) {
        // Fetch admin emails
        $adminStmt = $conn->prepare("SELECT email FROM users WHERE role = 'admin'");
        $adminStmt->execute();
        $adminEmails = $adminStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $adminStmt->close();

        // Send email notifications
        $companyName = "TCE Placement Cell";
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "studentplacement.tce@gmail.com"; // Replace with your SMTP email
        $mail->Password = "cntfmnvjuhcyfzyi"; // Replace with your SMTP password
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        $mail->setFrom("studentplacement.tce@gmail.com", $companyName); // Replace with your email
        $mail->isHTML(true);
        $mail->Subject = "New Job Posted";

        foreach ($adminEmails as $admin) {
            $mail->addAddress($admin['email']);
        }

        $mail->Body = "
            <h4>New Job Posted</h4>
            <p><b>Title:</b> $jobTitle</p>
            <p><b>Category:</b> $category</p>
            <p><b>Location:</b> $location</p>
        ";

        if ($mail->send()) {
            echo "<script>alert('Job posted successfully and notification sent!'); window.location.href='all-joblist.php';</script>";
        } else {
            echo "<script>alert('Job posted, but notification email failed to send.'); window.location.href='all-joblist.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to post the job. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='all-job-add.php';</script>";
    exit();
}

$conn->close();
?>
