<?php
session_start();
include 'config.php'; // Include the database configuration file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);
    $agree = isset($_POST['agree']);

    // Validate input fields
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password) || empty($role)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: signup.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: signup.php');
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: signup.php');
        exit();
    }

    if (!$agree) {
        $_SESSION['error'] = 'You must agree to the terms and conditions.';
        header('Location: signup.php');
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
        header('Location: signup.php');
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
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        $stmt->close();
        header('Location: signup.php');
        exit();
    }

    $conn->close();
} else {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: signup.php');
    exit();
}
?>
