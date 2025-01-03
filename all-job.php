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

// Fetch all jobs
$query = "
    SELECT jobs.id, jobs.title, jobs.type, jobs.location, jobs.category
    FROM jobs
";
$result = $conn->query($query);
?>

<main id="main" class="main">
    <div class="container">
        <h2>All Jobs</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S. No.</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $sno = 1; ?>
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($job['title']); ?></td>
                                <td><?= htmlspecialchars($job['type']); ?></td>
                                <td><?= htmlspecialchars($job['category']); ?></td>
                                <td><?= htmlspecialchars($job['location']); ?></td>
                                <td>
                                    <!-- View Button -->
                                    <a href="view-job.php?id=<?= $job['id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <!-- Delete Button -->
                                    <a href="delete-job-admin.php?id=<?= $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No jobs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
