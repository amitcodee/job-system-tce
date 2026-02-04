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

// Fetch the user's role
$userId = $_SESSION['user_id'];
$roleStmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$roleStmt->bind_param("i", $userId);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
if ($roleResult->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit();
}
$userRole = $roleResult->fetch_assoc()['role'];
$roleStmt->close();

// Restrict access to admin only
if ($userRole !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch jobs posted by this admin
$stmt = $conn->prepare(
    "SELECT id, title, type, location, salary, category, description, company_name
     FROM jobs
     WHERE posted_by = ?
     ORDER BY id DESC"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$jobs = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0">My Jobs (Admin)</h2>
            <a class="btn btn-primary" href="all-job-add.php">Add New Job</a>
        </div>

        <?php if ($jobs->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Job Title</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Salary</th>
                            <th>Company</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sno = 1; ?>
                        <?php while ($job = $jobs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($job['title'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($job['type'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($job['category'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($job['location'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($job['salary'] ?? 'N/A'); ?></td>
                                <td><?= htmlspecialchars($job['company_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $job['id']; ?>">View</button>
                                    <a href="edit-job.php?id=<?= $job['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete-job-admin.php?id=<?= $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal<?= $job['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $job['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $job['id']; ?>">Job Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Title:</strong> <?= htmlspecialchars($job['title'] ?? ''); ?></p>
                                            <p><strong>Type:</strong> <?= htmlspecialchars($job['type'] ?? ''); ?></p>
                                            <p><strong>Category:</strong> <?= htmlspecialchars($job['category'] ?? ''); ?></p>
                                            <p><strong>Location:</strong> <?= htmlspecialchars($job['location'] ?? ''); ?></p>
                                            <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary'] ?? 'N/A'); ?></p>
                                            <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name'] ?? 'N/A'); ?></p>
                                            <p><strong>Description:</strong> <?= strip_tags(htmlspecialchars_decode($job['description'] ?? '', ENT_QUOTES), '<p><br><strong><em><ul><ol><li><a><b><i><u><span><h1><h2><h3><h4><h5><h6>'); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No jobs found. <a href="all-job-add.php">Add a new job</a>.</p>
        <?php endif; ?>

        <?php $stmt->close(); ?>
        <?php $conn->close(); ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
