<?php
include 'config.php'; // Include database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch the user's role from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $role = $user['role'];
} else {
    // Handle case where user ID is not found
    echo "User not found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <?php if ($role === 'job_seeker'): ?>
            <!-- Job Seeker Links -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="job-seeker-profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="document-collection.php">
                    <i class="fas fa-folder"></i>
                    <span>Document Collection</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="resume.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Resume</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="jobs.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php elseif ($role === 'job_provider'): ?>
            <!-- Job Provider Links -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="post-job.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Post Job</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage-jobs.php">
                    <i class="fas fa-tasks"></i>
                    <span>Manage Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add-company.php">
                    <i class="fas fa-building"></i>
                    <span>Add Company</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage-companies.php">
                    <i class="fas fa-city"></i>
                    <span>Manage Companies</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="job-provider-profile.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php elseif ($role === 'admin'): ?>
            <!-- Admin Links -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-job-seekers.php">
                    <i class="fas fa-users"></i>
                    <span>All Job Seekers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-job-providers.php">
                    <i class="fas fa-user-tie"></i>
                    <span>All Job Providers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-companies.php">
                    <i class="fas fa-building"></i>
                    <span>All Companies</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-job.php">
                    <i class="fas fa-briefcase"></i>
                    <span>All Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="job_match.php">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Job Match</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="placement-data.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Placement Data</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all_message_query.php">
                    <i class="fas fa-envelope"></i>
                    <span>All Queries</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>
