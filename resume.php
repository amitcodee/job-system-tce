<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch existing resume data (if any) for the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM resumes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();
?>

<main id="main" class="main">
    <div class="container">
        <div class="overlay">
            <div class="overlay-message">
                <h2>This feature is under work</h2>
                <p>We're currently working on this feature. Please check back later.</p>
            </div>
        </div>

        <h2>Create Your Resume</h2>
        <form action="resume-submit.php" method="POST" enctype="multipart/form-data" class="row g-3">
            <!-- Personal Information -->
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($resume['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($resume['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone *</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($resume['phone'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Address *</label>
                <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($resume['address'] ?? '') ?>" required>
            </div>

            <!-- Educational Background -->
            <div class="col-md-12">
                <label for="education" class="form-label">Educational Background *</label>
                <textarea id="education" name="education" class="form-control" rows="5" placeholder="e.g., B.Sc in Computer Science, XYZ University, 2020" required><?= htmlspecialchars($resume['education'] ?? '') ?></textarea>
            </div>

            <!-- Work Experience -->
            <div class="col-md-12">
                <label for="experience" class="form-label">Work Experience (Optional)</label>
                <textarea id="experience" name="experience" class="form-control" rows="5" placeholder="e.g., Software Developer at ABC Company, Jan 2021 - Dec 2023"><?= htmlspecialchars($resume['experience'] ?? '') ?></textarea>
            </div>

            <!-- Skills -->
            <div class="col-md-12">
                <label for="skills" class="form-label">Skills *</label>
                <textarea id="skills" name="skills" class="form-control" rows="5" placeholder="e.g., PHP, JavaScript, HTML, CSS" required><?= htmlspecialchars($resume['skills'] ?? '') ?></textarea>
            </div>

            <!-- Submit -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save Resume</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
                <?php if ($resume): ?>
                    <a href="download-resume.php" class="btn btn-success">Download PDF</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>

<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .overlay-message {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }

    .overlay-message h2 {
        margin-bottom: 10px;
    }

    .overlay-message p {
        margin: 0;
    }
</style>
