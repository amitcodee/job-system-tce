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

// Admin dashboard stats
if ($userRole === 'admin') {
    // Jobs counts
    $jobCountAll = (int) ($conn->query("SELECT COUNT(*) AS count FROM jobs WHERE posted_by = {$userId}")->fetch_assoc()['count'] ?? 0);
    $jobCountToday = (int) ($conn->query("SELECT COUNT(*) AS count FROM jobs WHERE posted_by = {$userId} AND DATE(created_at) = CURDATE()")
        ->fetch_assoc()['count'] ?? 0);
    $jobCountWeek = (int) ($conn->query("SELECT COUNT(*) AS count FROM jobs WHERE posted_by = {$userId} AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")
        ->fetch_assoc()['count'] ?? 0);
    $jobCountMonth = (int) ($conn->query("SELECT COUNT(*) AS count FROM jobs WHERE posted_by = {$userId} AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")
        ->fetch_assoc()['count'] ?? 0);

    // Applications counts (only for admin's jobs)
    $appCountAllStmt = $conn->prepare(
        "SELECT COUNT(*) AS count
         FROM job_applications ja
         INNER JOIN jobs j ON ja.job_id = j.id
         WHERE j.posted_by = ?"
    );
    $appCountAllStmt->bind_param("i", $userId);
    $appCountAllStmt->execute();
    $appCountAll = (int) ($appCountAllStmt->get_result()->fetch_assoc()['count'] ?? 0);
    $appCountAllStmt->close();

    $appCountTodayStmt = $conn->prepare(
        "SELECT COUNT(*) AS count
         FROM job_applications ja
         INNER JOIN jobs j ON ja.job_id = j.id
         WHERE j.posted_by = ? AND DATE(ja.applied_at) = CURDATE()"
    );
    $appCountTodayStmt->bind_param("i", $userId);
    $appCountTodayStmt->execute();
    $appCountToday = (int) ($appCountTodayStmt->get_result()->fetch_assoc()['count'] ?? 0);
    $appCountTodayStmt->close();

    $appCountWeekStmt = $conn->prepare(
        "SELECT COUNT(*) AS count
         FROM job_applications ja
         INNER JOIN jobs j ON ja.job_id = j.id
         WHERE j.posted_by = ? AND ja.applied_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
    );
    $appCountWeekStmt->bind_param("i", $userId);
    $appCountWeekStmt->execute();
    $appCountWeek = (int) ($appCountWeekStmt->get_result()->fetch_assoc()['count'] ?? 0);
    $appCountWeekStmt->close();

    $appCountMonthStmt = $conn->prepare(
        "SELECT COUNT(*) AS count
         FROM job_applications ja
         INNER JOIN jobs j ON ja.job_id = j.id
         WHERE j.posted_by = ? AND ja.applied_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
    );
    $appCountMonthStmt->bind_param("i", $userId);
    $appCountMonthStmt->execute();
    $appCountMonth = (int) ($appCountMonthStmt->get_result()->fetch_assoc()['count'] ?? 0);
    $appCountMonthStmt->close();

    // Job seekers counts (system-wide)
    $seekerCountAll = (int) ($conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'job_seeker'")->fetch_assoc()['count'] ?? 0);
    $seekerCountToday = (int) ($conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'job_seeker' AND DATE(created_at) = CURDATE()")
        ->fetch_assoc()['count'] ?? 0);
    $seekerCountWeek = (int) ($conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'job_seeker' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")
        ->fetch_assoc()['count'] ?? 0);
    $seekerCountMonth = (int) ($conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'job_seeker' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")
        ->fetch_assoc()['count'] ?? 0);

    // Chart data: applications last 7 days
    $appTrendStmt = $conn->prepare(
        "SELECT DATE(ja.applied_at) AS day, COUNT(*) AS count
         FROM job_applications ja
         INNER JOIN jobs j ON ja.job_id = j.id
         WHERE j.posted_by = ? AND ja.applied_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(ja.applied_at)
         ORDER BY day"
    );
    $appTrendStmt->bind_param("i", $userId);
    $appTrendStmt->execute();
    $appTrendResult = $appTrendStmt->get_result();
    $appTrendMap = [];
    while ($r = $appTrendResult->fetch_assoc()) {
        $appTrendMap[$r['day']] = (int) $r['count'];
    }
    $appTrendStmt->close();

    $appTrendLabels = [];
    $appTrendCounts = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $appTrendLabels[] = date('d M', strtotime($date));
        $appTrendCounts[] = $appTrendMap[$date] ?? 0;
    }

    // Chart data: jobs last 6 months
    $jobTrendStmt = $conn->prepare(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS count
         FROM jobs
         WHERE posted_by = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
         GROUP BY ym
         ORDER BY ym"
    );
    $jobTrendStmt->bind_param("i", $userId);
    $jobTrendStmt->execute();
    $jobTrendResult = $jobTrendStmt->get_result();
    $jobTrendMap = [];
    while ($r = $jobTrendResult->fetch_assoc()) {
        $jobTrendMap[$r['ym']] = (int) $r['count'];
    }
    $jobTrendStmt->close();

    $jobTrendLabels = [];
    $jobTrendCounts = [];
    for ($i = 5; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime("-{$i} months"));
        $jobTrendLabels[] = date('M Y', strtotime($ym . '-01'));
        $jobTrendCounts[] = $jobTrendMap[$ym] ?? 0;
    }
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
                            <h5 class="card-title">Jobs (All Time)</h5>
                            <p class="card-text"><?= $jobCountAll ?> Jobs</p>
                            <a href="all-joblist.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Applications (All Time)</h5>
                            <p class="card-text"><?= $appCountAll ?> Applications</p>
                            <a href="all-applications.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Jobs Today</h5>
                            <p class="card-text"><?= $jobCountToday ?> Jobs</p>
                            <a href="all-joblist.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Jobs This Week</h5>
                            <p class="card-text"><?= $jobCountWeek ?> Jobs</p>
                            <a href="all-joblist.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Jobs This Month</h5>
                            <p class="card-text"><?= $jobCountMonth ?> Jobs</p>
                            <a href="all-joblist.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Applications Today</h5>
                            <p class="card-text"><?= $appCountToday ?> Applications</p>
                            <a href="all-applications.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Applications This Week</h5>
                            <p class="card-text"><?= $appCountWeek ?> Applications</p>
                            <a href="all-applications.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Applications This Month</h5>
                            <p class="card-text"><?= $appCountMonth ?> Applications</p>
                            <a href="all-applications.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Job Seekers Today</h5>
                            <p class="card-text"><?= $seekerCountToday ?> Seekers</p>
                            <a href="all-job-seekers.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Job Seekers This Week</h5>
                            <p class="card-text"><?= $seekerCountWeek ?> Seekers</p>
                            <a href="all-job-seekers.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Job Seekers This Month</h5>
                            <p class="card-text"><?= $seekerCountMonth ?> Seekers</p>
                            <a href="all-job-seekers.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Job Seekers (All Time)</h5>
                            <p class="card-text"><?= $seekerCountAll ?> Seekers</p>
                            <a href="all-job-seekers.php" class="btn btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Applications (Last 7 Days)</h5>
                            <canvas id="applicationsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Jobs (Last 6 Months)</h5>
                            <canvas id="jobsChart" height="120"></canvas>
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

<?php if ($userRole === 'admin'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const appLabels = <?= json_encode($appTrendLabels); ?>;
        const appData = <?= json_encode($appTrendCounts); ?>;
        const jobLabels = <?= json_encode($jobTrendLabels); ?>;
        const jobData = <?= json_encode($jobTrendCounts); ?>;

        const appCtx = document.getElementById('applicationsChart');
        if (appCtx) {
            new Chart(appCtx, {
                type: 'bar',
                data: {
                    labels: appLabels,
                    datasets: [{
                        label: 'Applications',
                        data: appData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, precision: 0 }
                    }
                }
            });
        }

        const jobCtx = document.getElementById('jobsChart');
        if (jobCtx) {
            new Chart(jobCtx, {
                type: 'line',
                data: {
                    labels: jobLabels,
                    datasets: [{
                        label: 'Jobs',
                        data: jobData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, precision: 0 }
                    }
                }
            });
        }
    </script>
<?php endif; ?>

<?php
include 'footer.php';
include 'common-footer.php';
?>
