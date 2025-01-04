<?php
session_start();
require_once 'php-mailer/PHPMailer.php';
require_once 'php-mailer/SMTP.php';
require_once 'php-mailer/Exception.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to submit preferences.'); window.location.href='login.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $jobTitle = trim($_POST['job_title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $salaryExpectation = trim($_POST['salary_expectation']);
    $additionalNotes = trim($_POST['additional_notes']);

    // Validate required fields
    if (empty($jobTitle) || empty($category) || empty($location)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Check if the user already has 4 preferences
    $stmt = $conn->prepare("SELECT COUNT(*) AS preference_count FROM job_preferences WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['preference_count'] >= 4) {
        echo "<script>alert('You can only create up to 4 job preferences.'); window.location.href='jobs.php';</script>";
        exit();
    }

    // Insert new preference
    $stmt = $conn->prepare("INSERT INTO job_preferences (user_id, job_title, category, location, salary_expectation, additional_notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $userId, $jobTitle, $category, $location, $salaryExpectation, $additionalNotes);

    if ($stmt->execute()) {
        // Fetch user name
        $userStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result()->fetch_assoc();
        $userName = $userResult['name'];
        $userStmt->close();

        // Fetch admin email
        $adminStmt = $conn->prepare("SELECT email FROM users WHERE role = 'admin' LIMIT 1");
        $adminStmt->execute();
        $adminResult = $adminStmt->get_result()->fetch_assoc();
        $adminEmail = $adminResult['email'];
        $adminStmt->close();

        // Send email notification
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
        $mail->addAddress($adminEmail);

        $mail->isHTML(true);
        $mail->Subject = "New Job Preference Added";
        $mail->Body = "
            <h4>New Job Preference Added</h4>
            <p><b>Added By:</b> $userName</p>
            <p><b>Title:</b> $jobTitle</p>
            <p><b>Category:</b> $category</p>
        ";

        if ($mail->send()) {
            echo "<script>alert('Job preference saved successfully and notification sent.'); window.location.href='jobs.php';</script>";
        } else {
            echo "<script>alert('Job preference saved, but failed to send notification email.'); window.location.href='jobs.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to save job preference. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='jobs.php';</script>";
    exit();
}
?>
