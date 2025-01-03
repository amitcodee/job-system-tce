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

// Validate the ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid job provider ID.'); window.location.href='all-job-providers.php';</script>";
    exit();
}

$providerId = intval($_GET['id']);

// Fetch job provider details
$stmt = $conn->prepare("SELECT name, email, phone, position, profile_description FROM users WHERE id = ? AND role = 'job_provider'");
$stmt->bind_param("i", $providerId);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$provider) {
    echo "<script>alert('Job provider not found.'); window.location.href='all-job-providers.php';</script>";
    exit();
}

// Fetch companies created by the job provider
$companyStmt = $conn->prepare("SELECT id, name, type, website FROM companies WHERE added_by = ?");
$companyStmt->bind_param("i", $providerId);
$companyStmt->execute();
$companies = $companyStmt->get_result();
?>
<main id="main" class="main">
    <div class="container">
        <h2>Job Provider Details</h2>

        <!-- Job Provider Information -->
        <div class="card mb-4">
            <div class="card-header">Personal Information</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($provider['name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($provider['email']); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($provider['phone']); ?></p>
                <p><strong>Position:</strong> <?= htmlspecialchars($provider['position'] ?? 'N/A'); ?></p>
                <p><strong>Profile Description:</strong> <?= nl2br(htmlspecialchars($provider['profile_description'] ?? 'N/A')); ?></p>
            </div>
        </div>

        <!-- Companies Created -->
        <div class="card mb-4">
            <div class="card-header">Companies Created</div>
            <div class="card-body">
                <?php if ($companies->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($company = $companies->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <p><strong>Company Name:</strong> <?= htmlspecialchars($company['name']); ?></p>
                                <p><strong>Type:</strong> <?= htmlspecialchars($company['type']); ?></p>
                                <p><strong>Website:</strong> <a href="<?= htmlspecialchars($company['website']); ?>" target="_blank"><?= htmlspecialchars($company['website']); ?></a></p>
                                <a href="view-company.php?id=<?= $company['id']; ?>" class="btn btn-primary btn-sm mt-2">View Company</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No companies created by this job provider.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
