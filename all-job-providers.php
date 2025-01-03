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

// Get the user's role from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}


// Fetch all job providers from the database
$stmt = $conn->prepare("SELECT id, name, email, phone, verified FROM users WHERE role = 'job_provider'");
$stmt->execute();
$jobProviders = $stmt->get_result();
?>
<main id="main" class="main">
    <div class="container">
        <h2>All Job Providers</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Verified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jobProviders->num_rows > 0): ?>
                        <?php $counter = 1; ?>
                        <?php while ($provider = $jobProviders->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++; ?></td>
                                <td><?= htmlspecialchars($provider['name']); ?></td>
                                <td><?= htmlspecialchars($provider['email']); ?></td>
                                <td><?= htmlspecialchars($provider['phone']); ?></td>
                                <td>
                                    <?php if ($provider['verified']): ?>
                                        <span class="badge bg-success">Verified</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Unverified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view-job-provider.php?id=<?= $provider['id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="delete-job-provider.php?id=<?= $provider['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this provider?');">Delete</a>
                                    <a href="toggle-verify-job-provider.php?id=<?= $provider['id']; ?>" class="btn btn-warning btn-sm">
                                        <?= $provider['verified'] ? 'Unverify' : 'Verify'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No job providers found.</td>
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
