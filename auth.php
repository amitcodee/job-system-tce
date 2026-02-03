<?php
// Start the session and check for errors
session_start();
if (!isset($_SESSION)) {
    die("Session is not initialized. Please check your server's session settings.");
}

include 'config.php'; // Include the database configuration file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='index.php';</script>";
        exit();
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['role'] === 'job_provider') {
            echo "<script>alert('This account type is no longer supported.'); window.location.href='index.php';</script>";
            exit();
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            header('Location: dashboard.php');
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid email or phone.'); window.location.href='index.php';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='index.php';</script>";
    exit();
}
?>
