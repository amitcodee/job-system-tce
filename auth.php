<?php
// Start the session and check for errors
ob_start();
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
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? OR phone = ? LIMIT 1");
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
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $redirectUrl = ($host ? ('//' . $host) : '') . $basePath . '/dashboard.php';
            header('Location: ' . $redirectUrl, true, 302);
            echo "<meta http-equiv='refresh' content='0;url={$redirectUrl}'>";
            echo "<script>window.location.href='" . $redirectUrl . "';</script>";
            ob_end_flush();
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
