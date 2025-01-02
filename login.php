<?php
// login.php

include('common-header.php');
include('config.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrMobile = $_POST['emailOrMobile'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($emailOrMobile) || empty($password)) {
        $error = 'Both fields are required!';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR mobile = ?) AND role = ?");
        $stmt->bind_param("sss", $emailOrMobile, $emailOrMobile, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user'] = $user;
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid password!';
            }
        } else {
            $error = 'No account found for the selected role!';
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h3 class="text-center mb-4 fw-bold text-primary">Welcome Back!</h3>
                    <?php if (!empty($error)): ?>
                        <span class="text-danger d-block mb-3 text-center fw-semibold" id="error-message"><?php echo $error; ?></span>
                        <script>
                            setTimeout(() => {
                                const errorMessage = document.getElementById('error-message');
                                if (errorMessage) errorMessage.remove();
                            }, 3000);
                        </script>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="emailOrMobile" class="form-label fw-semibold">Email or Mobile</label>
                            <input type="text" class="form-control rounded-pill shadow-sm" id="emailOrMobile" name="emailOrMobile" placeholder="Enter your email or mobile" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control rounded-pill shadow-sm" id="password" name="password" placeholder="Enter your password" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3">
                                <i class="fas fa-eye-slash" id="toggle-password" style="cursor: pointer;"></i>
                            </span>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">Login As</label>
                            <select class="form-select rounded-pill shadow-sm" id="role" name="role" required>
                                <option value="job_seeker">Job Seeker</option>
                                <option value="recruiter">Recruiter</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <a href="forgot-password.php" class="text-decoration-none text-primary">Forgot Password?</a>
                            <a href="signup.php" class="text-decoration-none text-primary">Create Account</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('common-footer.php'); ?>
