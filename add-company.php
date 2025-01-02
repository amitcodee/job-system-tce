<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
?>

<main id="main" class="main">
    <div class="container">
        <h2>Add Company</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?= $error; ?> </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?= $success; ?> </div>
        <?php endif; ?>

        <form action="add-company-submit.php" method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-12">
                <label for="company_name" class="form-label">Company Name *</label>
                <input type="text" id="company_name" name="company_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="company_type" class="form-label">Company Type *</label>
                <input type="text" id="company_type" name="company_type" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="company_website" class="form-label">Company Website</label>
                <input type="url" id="company_website" name="company_website" class="form-control">
            </div>

            <div class="col-md-6">
                <label for="company_email" class="form-label">Company Email *</label>
                <input type="email" id="company_email" name="company_email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="company_phone" class="form-label">Company Phone *</label>
                <input type="text" id="company_phone" name="company_phone" class="form-control" required>
            </div>

            <div class="col-12">
                <label for="company_address" class="form-label">Company Address *</label>
                <textarea id="company_address" name="company_address" class="form-control" placeholder="1234 Main St" required></textarea>
            </div>

            <div class="col-12">
                <label for="company_description" class="form-label">Company Description</label>
                <textarea id="company_description" name="company_description" class="form-control" placeholder="Brief description about the company"></textarea>
            </div>

            <div class="col-md-6">
                <label for="company_logo" class="form-label">Company Logo</label>
                <input type="file" id="company_logo" name="company_logo" class="form-control">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Add Company</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</main>

<?php 
include 'footer.php';
include 'common-footer.php';
?>
