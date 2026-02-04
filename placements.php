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

// Fetch placements
$placementStmt = $conn->prepare(
    "SELECT p.id, p.company_name, p.profile, p.remarks, p.created_at, u.name AS user_name, u.email AS user_email
     FROM placements p
     INNER JOIN users u ON p.user_id = u.id
     ORDER BY p.created_at DESC"
);
$placementStmt->execute();
$placements = $placementStmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0">Placements</h2>
        </div>

        <?php if ($placements->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Job Seeker</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Profile</th>
                            <th>Remarks</th>
                            <th>Placed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sno = 1; ?>
                        <?php while ($row = $placements->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($row['user_name'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['user_email'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['company_name'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['profile'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['remarks'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['created_at'] ?? ''); ?></td>
                                <td>
                                    <a class="btn btn-sm btn-warning" href="unplace-placement.php?id=<?= (int) $row['id']; ?>" onclick="return confirm('Mark as unplaced?');">Unplace</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No placements found.</p>
        <?php endif; ?>

        <?php $placementStmt->close(); ?>
        <?php $conn->close(); ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
