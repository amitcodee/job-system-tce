<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

if (!isset($conn) || !$conn) {
    echo "<script>alert('Database connection error. Please contact admin.'); window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='index.php';</script>";
        exit();
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? OR phone = ? LIMIT 1");
    if (!$stmt) {
        echo "<script>alert('Server error. Please contact admin.'); window.location.href='index.php';</script>";
        exit();
    }
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($dbId, $dbName, $dbPassword, $dbRole);
        $stmt->fetch();

        if ($dbRole === 'job_provider') {
            echo "<script>alert('This account type is no longer supported.'); window.location.href='index.php';</script>";
            exit();
        }

        if ($dbRole === 'deactive') {
            echo "<script>alert('Your account is deactivated. Please contact admin.'); window.location.href='index.php';</script>";
            exit();
        }

        // Verify password
        if (password_verify($password, $dbPassword)) {
            $_SESSION['user_id'] = $dbId;
            $_SESSION['username'] = $dbName;
            echo "<script>window.location.href='dashboard.php';</script>";
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
