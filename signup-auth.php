<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
    $agree = isset($_POST['agree']);

    // Validate input fields
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password) || empty($role) || empty($captcha)) {
        $_SESSION['error'] = 'All fields are required.';
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    if (!isset($_SESSION['signup_captcha']) || (int)$captcha !== (int)$_SESSION['signup_captcha']) {
        $_SESSION['error'] = 'Invalid captcha answer.';
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    unset($_SESSION['signup_captcha']);

    if ($role !== 'job_seeker') {
        $_SESSION['error'] = 'Only job seeker registration is allowed.';
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    if (!$agree) {
        $_SESSION['error'] = 'You must agree to the terms and conditions.';
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    // Check if email or phone already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = 'Email or phone number already exists.';
        $stmt->close();
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    $stmt->close();

    // Insert the new user into the database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful! Please log in.';
        $stmt->close();
        echo "<script>alert('Registration successful! Please log in.'); window.location.href='login.php';</script>";
        exit();
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        $stmt->close();
        echo "<script>window.location.href='sign-up.php';</script>";
        exit();
    }

    $conn->close();
} else {
    $_SESSION['error'] = 'Invalid request method.';
    echo "<script>window.location.href='sign-up.php';</script>";
    exit();
}
?>
