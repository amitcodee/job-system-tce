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
                                <!-- <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQP6Zh9d1FIeutRKFbg-0EhB2czdDgwEoTLOw&s" alt=""> -->
                                <span class="d-none d-lg-block">TCE</span>
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Forgot Password</h5>
                                    <p class="text-center small">Enter your email or phone to reset your password</p>
                                </div>

                                <form class="row g-3 needs-validation" novalidate action="forgot-password-handler.php" method="POST">

                                    <div class="col-12">
                                        <label for="emailOrPhone" class="form-label">Email or Phone</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">
                                                <i class="fa-solid fa-envelope"></i>
                                            </span>
                                            <input type="text" name="email_or_phone" class="form-control" id="emailOrPhone" required>
                                            <div class="invalid-feedback">Please enter your email or phone number.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Send Reset Link</button>
                                    </div>
                                    <div class="col-12 text-center">
                                        <a href="login.php" class="small">Back to Login</a>
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
    // Add any specific JS if required later
</script>
<?php
include 'common-footer.php';
?>
