<?php
// authenticate.php

session_start();
include('config.php');

// Retrieve form data
$emailOrMobile = $_POST['emailOrMobile'] ?? '';
$password = $_POST['password'] ?? '';

// Validate user input
if (empty($emailOrMobile) || empty($password)) {
    echo "<script>alert('Both fields are required!'); window.location.href='login.php';</script>";
    exit();
}

// Authenticate user
$stmt = $conn->prepare("SELECT email, mobile, password FROM users WHERE email = ? OR mobile = ?");
$stmt->bind_param("ss", $emailOrMobile, $emailOrMobile);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'email' => $user['email'],
            'mobile' => $user['mobile']
        ];
        echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid password!'); window.location.href='login.php';</script>";
    }
} else {
    echo "<script>alert('No user found with these credentials!'); window.location.href='login.php';</script>";
}

$stmt->close();
$conn->close();
?>
