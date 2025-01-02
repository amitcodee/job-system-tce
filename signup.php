<?php
// signup.php

include('common-header.php');
include('config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = $_POST['role'] ?? 'job_seeker';
    $agreeTerms = isset($_POST['agreeTerms']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required!';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match!';
    } elseif (!$agreeTerms) {
        $error = 'You must agree to the terms and conditions!';
    } else {
        // Check if email and mobile already exist for the same role
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND mobile = ? AND role = ?");
        $stmt->bind_param("sss", $email, $mobile, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email and mobile number already used for this role!';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO users (name, email, mobile, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $mobile, $hashedPassword, $role);

            if ($stmt->execute()) {
                header('Location: login.php');
                exit();
            } else {
                $error = 'Error: Unable to create account.';
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h3 class="text-center mb-4 fw-bold text-primary">Create an Account</h3>
                    <?php if (!empty($error)): ?>
                        <span class="text-danger d-block mb-3 text-center fw-semibold" id="error-message"><?php echo $error; ?></span>
                        <script>
                            setTimeout(() => {
                                const errorMessage = document.getElementById('error-message');
                                if (errorMessage) errorMessage.remove();
                            }, 3000);
                        </script>
                    <?php endif; ?>
                    <form action="signup.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control rounded-pill shadow-sm" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control rounded-pill shadow-sm" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label fw-semibold">Mobile</label>
                            <input type="text" class="form-control rounded-pill shadow-sm" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control rounded-pill shadow-sm" id="password" name="password" placeholder="Create a password" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control rounded-pill shadow-sm" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3">
                                <i class="fas fa-eye-slash" id="toggle-password" style="cursor: pointer;"></i>
                            </span>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">Role</label>
                            <select class="form-select rounded-pill shadow-sm" id="role" name="role" required>
                                <option value="job_seeker" selected>Job Seeker</option>
                                <option value="recruiter">Recruiter</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agreeTerms" name="agreeTerms">
                            <label for="agreeTerms" class="form-check-label">I agree to the <a href="terms.php" class="text-decoration-none text-primary">Terms and Conditions</a></label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include('common-footer.php'); ?>
