<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">
        <h2>Account Settings</h2>

        <div class="card mb-4">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <form id="changePasswordForm" action="update-password.php" method="POST">
                    <div class="mb-3">
                        <label for="new_password" class="form-label"><strong>New Password:</strong></label>
                        <div class="input-group">
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                            <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="button" id="generatePassword" class="btn btn-secondary">Generate Random Password</button>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Password</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('new_password');
        const passwordIcon = this.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordField.type = 'password';
            passwordIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Generate random password
    document.getElementById('generatePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('new_password');
        const randomPassword = Math.random().toString(36).slice(-10); // Generates a 10-character random string
        passwordField.value = randomPassword;
    });
</script>

<?php
include 'footer.php';
include 'common-footer.php';
?>
