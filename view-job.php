<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Validate job ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid job ID.'); window.location.href='job_match.php';</script>";
    exit();
}

$jobId = intval($_GET['id']);

// Fetch job details
$jobQuery = "
    SELECT jobs.*, users.name AS provider_name, users.email AS provider_email, users.phone AS provider_phone, 
           companies.name AS company_name, companies.type AS company_type, companies.website AS company_website 
    FROM jobs 
    JOIN users ON jobs.posted_by = users.id 
    LEFT JOIN companies ON jobs.company_id = companies.id 
    WHERE jobs.id = ?
";
$jobStmt = $conn->prepare($jobQuery);
$jobStmt->bind_param("i", $jobId);
$jobStmt->execute();
$job = $jobStmt->get_result()->fetch_assoc();
$jobStmt->close();

if (!$job) {
    echo "<script>alert('Job not found.'); window.location.href='job_match.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Details</h2>

        <!-- Job Information -->
        <div class="card mb-4">
            <div class="card-header">Job Information</div>
            <div class="card-body">
                <p><strong>Title:</strong> <?= htmlspecialchars($job['title']); ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($job['type']); ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($job['category']); ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($job['location']); ?></p>
                <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary'] ?? 'N/A'); ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($job['description'] ?? 'N/A')); ?></p>
                <p><strong>Posted On:</strong> <?= htmlspecialchars($job['created_at']); ?></p>
            </div>
        </div>

        <!-- Company Information -->
        <?php if (!empty($job['company_name'])): ?>
    <div class="card mb-4">
        <div class="card-header">Company Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($job['company_name']); ?></p>
            <p><strong>Type:</strong> <?= htmlspecialchars($job['company_type']); ?></p>
            <p><strong>Website:</strong> <?= !empty($job['company_website']) ? '<a href="' . htmlspecialchars($job['company_website']) . '" target="_blank">' . htmlspecialchars($job['company_website']) . '</a>' : 'N/A'; ?></p>
            <a href="view-company.php?id=<?= htmlspecialchars($job['company_id']); ?>" class="btn btn-primary mt-3">View Full Company Details</a>
        </div>
    </div>
<?php endif; ?>


        <!-- Job Provider Information -->
        <div class="card mb-4">
            <div class="card-header">Job Provider Information</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($job['provider_name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($job['provider_email']); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($job['provider_phone']); ?></p>
            </div>
        </div>

        <!-- Apply or Back -->
        <div class="mt-4">
            <a href="job_match.php" class="btn btn-secondary">Back to Job Listings</a>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
