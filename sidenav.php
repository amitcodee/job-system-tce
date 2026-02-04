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
                <a class="nav-link" href="jobshow.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="interview-resume-help.php">
                    <i class="fas fa-lightbulb"></i>
                    <span>Interview & Resume Help</span>
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
                <a class="nav-link" href="all-job-add.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Job</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-joblist.php">
                    <i class="fas fa-briefcase"></i>
                    <span>My Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-applications.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Applications</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-job-seekers.php">
                    <i class="fas fa-users"></i>
                    <span>Job Seekers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all_message_query.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="placements.php">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Placements</span>
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
