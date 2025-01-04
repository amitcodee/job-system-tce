<?php
session_start();
require_once 'php-mailer/PHPMailer.php';
require_once 'php-mailer/SMTP.php';
require_once 'php-mailer/Exception.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email_or_phone']);
    
    // Validate input
    if (empty($email)) {
        echo "<script>alert('Email or phone is required.'); window.history.back();</script>";
        exit();
    }

    // Check if the account exists
    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('No account found with this email or phone.'); window.history.back();</script>";
        exit();
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];
    $userName = $user['name'];
    $userEmail = $user['email'];
    $stmt->close();

    // Generate a unique reset token
    $resetToken = bin2hex(random_bytes(16));
    $resetExpiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // Store the reset token in the database
    $updateStmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
    $updateStmt->bind_param("ssi", $resetToken, $resetExpiry, $userId);
    if (!$updateStmt->execute()) {
        echo "<script>alert('Failed to process the request. Please try again later.'); window.history.back();</script>";
        exit();
    }
    $updateStmt->close();

    $resetLink = "http://localhost/syntax-work/job-system-tce/reset-password.php?token=$resetToken";
    $portalLink = "https://yourwebsite.com/";
    $companyName = "Your Company";
    $year = date("Y");

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "panel.tce@gmail.com"; // Your SMTP email
    $mail->Password = "azlmtcraonajceci"; // Your SMTP password
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';

    $mail->setFrom("panel.tce@gmail.com", $companyName);
    $mail->addAddress($userEmail, $userName);

    $mail->isHTML(true);
    $mail->Subject = "Password Reset Request";
    $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .button { display: inline-block; padding: 10px 20px; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <h3>Password Reset Request</h3>
            <p>Dear <b>$userName</b>,</p>
            <p>We received a request to reset your password. Click the link below to reset your password:</p>
            <p><a href='$resetLink' class='button'>Reset Password</a></p>
            <p>If you did not request this, please ignore this email.</p>
            <p>Best Regards,<br>$companyName Team</p>
            <footer>&copy; $year $companyName. All Rights Reserved.</footer>
        </body>
        </html>
    ";

    if ($mail->send()) {
        echo "<script>alert('Password reset link has been sent to your email.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Failed to send the email. Please try again later.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
    exit();
}

$conn->close();
?>
