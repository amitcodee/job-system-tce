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
                                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                    <p class="text-center small">Enter your email/phone & password to login</p>
                                </div>

                                <form class="row g-3 needs-validation" novalidate action="auth.php" method="POST">

                                    <div class="col-12">
                                        <label for="yourUsername" class="form-label">Email or Phone</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">
                                                <i class="fa-solid fa-envelope"></i>
                                            </span>
                                            <input type="text" name="username" class="form-control" id="yourUsername" required>
                                            <div class="invalid-feedback">Please enter your email or phone number.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPassword" class="form-label">Password</label>
                                        <div class="input-group has-validation">
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </span>
                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>
                                    </div>

                                  
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Login</button>
                                    </div>
                                    <div class="col-12 text-center">
                                        <a href="forgot-password.php" class="small">Forgot your password?</a>
                                    </div>
                                    <div class="col-12">
                                        <p class="small mb-0">Don't have account? <a href="sign-up.php">Create an account</a></p>
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


<script>
    document.querySelector('.toggle-password').addEventListener('click', function() {
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
</script>
<?php
include 'common-footer.php';
?>