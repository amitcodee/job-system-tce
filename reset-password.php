<?php
session_start();
include 'config.php';

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    echo "<script>alert('Invalid or missing token.'); window.location.href='login.php';</script>";
    exit();
}

$token = $_GET['token'];

// Check if the token exists and is valid
$stmt = $conn->prepare("SELECT id, reset_expiry FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Invalid or expired token.'); window.location.href='login.php';</script>";
    exit();
}

$user = $result->fetch_assoc();
$userId = $user['id'];
$resetExpiry = $user['reset_expiry'];

// Check if the token has expired
if (strtotime($resetExpiry) < time()) {
    echo "<script>alert('This token has expired. Please request a new password reset.'); window.location.href='forgot-password.php';</script>";
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate passwords
    if (empty($newPassword) || empty($confirmPassword)) {
        echo "<script>alert('Both password fields are required.'); window.history.back();</script>";
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the user's password and clear the reset token
    $updateStmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
    $updateStmt->bind_param("si", $hashedPassword, $userId);

    if ($updateStmt->execute()) {
        echo "<script>alert('Your password has been successfully reset.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Failed to reset your password. Please try again.'); window.history.back();</script>";
    }

    $updateStmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1d2b64, #f8cdda);
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .card {
            border: none;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: #1d2b64;
            border: none;
        }
        .btn-primary:hover {
            background: #162047;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-label {
            font-weight: bold;
        }
        .section {
            min-height: 100vh;
        }
        .toggle-password {
            cursor: pointer;
        }
    </style>
</head>
<body>
<main>
    <div class="container">
        <section class="section register d-flex flex-column align-items-center justify-content-center py-4">
            <div class="col-lg-5 col-md-8 d-flex flex-column align-items-center justify-content-center">
                <div class="card p-4">
                    <div class="card-body">
                        <h4 class="card-title text-center text-primary mb-4">Reset Your Password</h4>
                        <form action="" method="POST">
                            <div class="mb-3 position-relative">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
                                    <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('new_password', 'toggleIcon1')">
                                        <i id="toggleIcon1" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3 position-relative">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                                    <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('confirm_password', 'toggleIcon2')">
                                        <i id="toggleIcon2" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/0fffda5efb.js" crossorigin="anonymous"></script>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>
