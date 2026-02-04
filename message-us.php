<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

?>

<main id="main" class="main">
    <section class="section contact">

        <?php if (isset($_GET['sent'])): ?>
            <div class="alert alert-success">Your message has been sent successfully.</div>
        <?php endif; ?>

        <div class="row gy-4">

            <div class="col-xl-6">

                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="info-box card">
                            <i class="bi bi-geo-alt"></i>
                            <h3>Address</h3>
                            <p>Shop No 4, City Center, near Bus Stand, Model Town, Hoshiarpur, Punjab 146001</p><br>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="info-box card">
                            <i class="bi bi-telephone"></i>
                            <h3>Call Us</h3>
                            <p>+91 9888122442</p><br>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="info-box card">
                            <i class="bi bi-envelope"></i>
                            <h3>Email Us</h3>
                            <p>jyotiacad.techcadd@gmail.com<br>hrmtechcadd@gmail.com</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-6">
                <div class="card p-4">
                    <form action="contact.php" method="post" class="php-email-form">

                        <div class="row gy-4">

                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                            </div>

                            <div class="col-md-6 ">
                                <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                            </div>

                            <div class="col-md-12">
                                <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                            </div>

                            <div class="col-md-12">
                                <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                            </div>

                            <div class="col-md-12 text-center">
                                <div class="loading">Loading</div>
                                <div class="error-message"></div>
                                <div class="sent-message">Your message has been sent. Thank you!</div>

                                <button type="submit">Send Message</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>

        </div>

    </section>
</main>
<?php
include 'footer.php';
include 'common-footer.php';
?>
<script>

</script>