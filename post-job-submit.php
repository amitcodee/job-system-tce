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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobTitle = trim($_POST['job_title']);
    $jobType = trim($_POST['job_type']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $category = trim($_POST['category']);
    $companyId = intval($_POST['company_id']);
    $description = trim($_POST['description']);

    // Validate required fields
    if (empty($jobTitle) || empty($jobType) || empty($location) || empty($category) || empty($companyId) || empty($description)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Insert job into the database
    $stmt = $conn->prepare("INSERT INTO jobs (title, type, location, salary, category, company_id, description, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $userId = $_SESSION['user_id'];
    $stmt->bind_param("sssssssi", $jobTitle, $jobType, $location, $salary, $category, $companyId, $description, $userId);

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
        $mail->Username = "panel.tce@gmail.com"; // Replace with your SMTP email
        $mail->Password = "azlmtcraonajceci"; // Replace with your SMTP password
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        $mail->setFrom("panel.tce@gmail.com", $companyName); // Replace with your email
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
            echo "<script>alert('Job posted successfully and notification sent!'); window.location.href='manage-jobs.php';</script>";
        } else {
            echo "<script>alert('Job posted, but notification email failed to send.'); window.location.href='manage-jobs.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to post the job. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='post-job.php';</script>";
    exit();
}

$conn->close();
?>
