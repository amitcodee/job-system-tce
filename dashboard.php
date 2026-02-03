<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch user role from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userRoleResult = $stmt->get_result();
$userRole = $userRoleResult->fetch_assoc()['role'] ?? null;
$stmt->close();

if (!$userRole || !in_array($userRole, ['admin', 'job_seeker'], true)) {
    echo "<script>alert('Invalid user role.'); window.location.href='logout.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">
        <h2>Dashboard</h2>
        <div class="row">
            <?php if ($userRole === 'admin'): ?>
                <!-- Admin Dashboard -->
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">All Jobs</h5>
                            <?php
                            $jobCount = $conn->query("SELECT COUNT(*) AS count FROM jobs")->fetch_assoc()['count'];
                            ?>
                            <p class="card-text"><?= $jobCount ?> Jobs</p>
                            <a href="all-joblist.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
            <script>
                window.location.href = 'job_seeker_dashboard.php';
            </script>
               
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
