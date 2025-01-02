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
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="document-collection.php">
                    <i class="bi bi-folder"></i>
                    <span>Document Collection</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="resume.php">
                    <i class="bi bi-file-earmark-person"></i>
                    <span>Resume</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="jobs.php">
                    <i class="bi bi-briefcase"></i>
                    <span>Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php elseif ($role === 'job_provider'): ?>
            <!-- Job Provider Links -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="post-job.php">
                    <i class="bi bi-plus-square"></i>
                    <span>Post Job</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage-jobs.php">
                    <i class="bi bi-pencil-square"></i>
                    <span>Manage Jobs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add-company.php">
                    <i class="bi bi-building"></i>
                    <span>Add Company</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage-companies.php">
                    <i class="bi bi-building"></i>
                    <span>Manage Companies</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">
                    <i class="bi bi-chat"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php elseif ($role === 'admin'): ?>
            <!-- Admin Links -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-job-seekers.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>All Job Seekers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-job-providers.php">
                    <i class="bi bi-people"></i>
                    <span>All Job Providers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all-companies.php">
                    <i class="bi bi-building"></i>
                    <span>All Companies</span>
                </a>
            </li>
          
            <li class="nav-item">
                <a class="nav-link" href="messages.php">
                    <i class="bi bi-chat"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>