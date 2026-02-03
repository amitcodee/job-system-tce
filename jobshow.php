<?php
session_start();
include 'common-header.php';
include 'config.php';

// Public page: login required only for apply action

// Fetch all jobs
mysqli_report(MYSQLI_REPORT_OFF);
$query = "
    SELECT id, title, type, location, salary, category, description, company_name, company_website
    FROM jobs
    ORDER BY id DESC
";
$result = $conn->query($query);

if (isset($_SESSION['user_id'])) {
    include 'header.php';
    include 'sidenav.php';
}
?>

<main id="main" class="main">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0">Available Jobs</h2>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($job = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($job['title'] ?? ''); ?></h5>
                                <p class="text-muted mb-2"><?= htmlspecialchars($job['company_name'] ?? 'N/A'); ?></p>
                                <p class="mb-1"><strong>Type:</strong> <?= htmlspecialchars($job['type'] ?? ''); ?></p>
                                <p class="mb-1"><strong>Category:</strong> <?= htmlspecialchars($job['category'] ?? ''); ?></p>
                                <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($job['location'] ?? ''); ?></p>
                                <p class="mb-3"><strong>Salary:</strong> <?= htmlspecialchars($job['salary'] ?? 'N/A'); ?></p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#jobModal<?= $job['id']; ?>">View Details</button>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details Modal -->
                    <div class="modal fade" id="jobModal<?= $job['id']; ?>" tabindex="-1" aria-labelledby="jobModalLabel<?= $job['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="jobModalLabel<?= $job['id']; ?>">Job Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Title:</strong> <?= htmlspecialchars($job['title'] ?? ''); ?></p>
                                    <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name'] ?? 'N/A'); ?></p>
                                    <?php
                                    $rawDescription = $job['description'] ?? '';
                                    $companyWebsite = $job['company_website'] ?? '';
                                    if (empty($rawDescription) && !empty($companyWebsite) && preg_match('/<[^>]+>/', $companyWebsite)) {
                                        $rawDescription = $companyWebsite;
                                        $companyWebsite = '';
                                    }
                                    ?>
                                    <?php if (!empty($companyWebsite) && filter_var($companyWebsite, FILTER_VALIDATE_URL)): ?>
                                        <p><strong>Website:</strong> <a href="<?= htmlspecialchars($companyWebsite); ?>" target="_blank"><?= htmlspecialchars($companyWebsite); ?></a></p>
                                    <?php endif; ?>
                                    <p><strong>Type:</strong> <?= htmlspecialchars($job['type'] ?? ''); ?></p>
                                    <p><strong>Category:</strong> <?= htmlspecialchars($job['category'] ?? ''); ?></p>
                                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location'] ?? ''); ?></p>
                                    <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary'] ?? 'N/A'); ?></p>
                                    <p><strong>Description:</strong></p>
                                    <div class="border rounded p-3">
                                        <?php
                                        $allowedTags = '<p><br><ul><ol><li><strong><b><em><i><u><a><table><thead><tbody><tr><th><td>';
                                        echo strip_tags($rawDescription, $allowedTags);
                                        ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a class="btn btn-success" href="job_seeker_dashboard.php">Apply Now</a>
                                    <?php else: ?>
                                        <a class="btn btn-success" href="login.php">Login to Apply</a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php elseif ($result === false): ?>
            <p>Jobs are not available right now. Please try again later.</p>
        <?php else: ?>
            <p>No jobs found.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
