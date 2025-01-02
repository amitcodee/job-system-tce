<?php
include 'common-header.php';
?>
<main>
    <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            <a href="index.html" class="logo d-flex align-items-center w-auto">
                                <img src="assets/img/logo.png" alt="">
                                <span class="d-none d-lg-block">NiceAdmin</span>
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Create Your Account</h5>
                                    <p class="text-center small">Fill in the details to register</p>
                                </div>

                                <form class="row g-3 needs-validation" novalidate action="signup-auth.php" method="POST" onsubmit="return validatePasswords()">
                                    <div class="col-12">
                                        <label for="yourName" class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" id="yourName" required>
                                        <div class="invalid-feedback">Please enter your name.</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourEmail" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" id="yourEmail" required>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPhone" class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" id="yourPhone" required>
                                        <div class="invalid-feedback">Please enter your phone number.</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPassword" class="form-label">Password</label>
                                        <div class="input-group has-validation">
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </span>
                                            <div class="invalid-feedback">Please enter your password.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                                        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
                                        <div class="invalid-feedback" id="confirmPasswordFeedback">Please confirm your password.</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="role" class="form-label">Role</label>
                                        <select name="role" id="role" class="form-select" required>
                                            <option value="">Select Role</option>
                                            <option value="job_seeker">Job Seeker</option>
                                            <option value="recruiter">Recruiter</option>
                                        </select>
                                        <div class="invalid-feedback">Please select your role.</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="agree" id="agreeTerms" required>
                                            <label class="form-check-label" for="agreeTerms">
                                                I agree to the <a href="terms.php">terms and conditions</a>
                                            </label>
                                            <div class="invalid-feedback">You must agree before submitting.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Sign Up</button>
                                    </div>

                                    <div class="col-12">
                                        <p class="small mb-0">Already have an account? <a href="login.php">Log in</a></p>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>

    </div>
</main>
<?php
include 'common-footer.php';
?>

<script>
    document.querySelector('.toggle-password').addEventListener('click', function () {
        const passwordInput = document.getElementById('yourPassword');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    function validatePasswords() {
        const password = document.getElementById('yourPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');

        if (password.value !== confirmPassword.value) {
            confirmPassword.classList.add('is-invalid');
            confirmPasswordFeedback.textContent = 'Passwords do not match.';
            return false;
        } else {
            confirmPassword.classList.remove('is-invalid');
            confirmPasswordFeedback.textContent = '';
            return true;
        }
    }

    // Automatically remove "Passwords do not match" message on input
    document.getElementById('confirmPassword').addEventListener('input', function () {
        const password = document.getElementById('yourPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');

        if (password === confirmPassword) {
            this.classList.remove('is-invalid');
            confirmPasswordFeedback.textContent = '';
        }
    });
</script>
