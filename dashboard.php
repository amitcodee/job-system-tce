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

if (!$userRole) {
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
                            <a href="all-job.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Job Seekers</h5>
                            <?php
                            $seekerCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'job_seeker'")->fetch_assoc()['count'];
                            ?>
                            <p class="card-text"><?= $seekerCount ?> Job Seekers</p>
                            <a href="all-job-seekers.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Job Providers</h5>
                            <?php
                            $providerCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'job_provider'")->fetch_assoc()['count'];
                            ?>
                            <p class="card-text"><?= $providerCount ?> Job Providers</p>
                            <a href="all-job-providers.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Companies</h5>
                            <?php
                            $companyCount = $conn->query("SELECT COUNT(*) AS count FROM companies")->fetch_assoc()['count'];
                            ?>
                            <p class="card-text"><?= $companyCount ?> Companies</p>
                            <a href="all-companies.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            <?php elseif ($userRole === 'job_provider'): ?>
                <!-- Job Provider Dashboard -->
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">My Jobs</h5>
                            <?php
                            $jobCount = $conn->query("SELECT COUNT(*) AS count FROM jobs WHERE posted_by = $userId")->fetch_assoc()['count'];
                            ?>
                            <p class="card-text"><?= $jobCount ?> Jobs</p>
                            <a href="my-jobs.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">My Companies</h5>
                            <?php
                            $companyCount = $conn->query("SELECT COUNT(*) AS count FROM companies WHERE added_by = $userId")->fetch_assoc()['count'];
                            ?>
                            <p class="card-text"><?= $companyCount ?> Companies</p>
                            <a href="my-companies.php" class="btn btn-primary">View All</a>
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
