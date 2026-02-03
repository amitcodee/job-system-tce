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

// Fetch all job seekers
$stmt = $conn->prepare("SELECT id, name, email, phone, created_at FROM users WHERE role = 'job_seeker' ORDER BY id DESC");
$stmt->execute();
$seekers = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Seekers</h2>

        <?php if ($seekers->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sno = 1; ?>
                        <?php while ($seeker = $seekers->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($seeker['name'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($seeker['email'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($seeker['phone'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($seeker['created_at'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No job seekers found.</p>
        <?php endif; ?>

        <?php $stmt->close(); ?>
        <?php $conn->close(); ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
