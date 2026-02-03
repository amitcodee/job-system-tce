<?php
include 'config.php';

// Ensure the session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch user details from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user record
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userName = htmlspecialchars($user['name']);
    $userRole = htmlspecialchars($user['role']);
    $userImage = 'assets/img/default-profile.png'; // Default profile image
} else {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit();
}

$stmt->close();
?>
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <img src="./logo.png" alt="Logo">
            <span class="d-none d-lg-block">
                TCE Placement Cell
            </span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                  <i class="bi bi-person-circle fs-5"></i>
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?= $userName ?></span>
                </a><!-- End Profile Image Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
    <li class="dropdown-header">
        <h6><?= htmlspecialchars($userName); ?></h6>
        <span><?= ucfirst(htmlspecialchars($userRole)); ?></span>
    </li>
    <li>
        <hr class="dropdown-divider">
    </li>

    <li>
        <a class="dropdown-item d-flex align-items-center" href="<?php
            // Redirect to specific profile pages based on the user role
            if ($userRole === 'admin') {
                echo 'admin-profile.php';
            } elseif ($userRole === 'job_seeker') {
                echo 'job-seeker-profile.php';
            } else {
                echo 'profile.php'; // Fallback to a generic profile page
            }
        ?>">
            <i class="bi bi-person"></i>
            <span>My Profile</span>
        </a>
    </li>
    <li>
        <hr class="dropdown-divider">
    </li>

    <li>
        <a class="dropdown-item d-flex align-items-center" href="account-settings.php">
            <i class="bi bi-gear"></i>
            <span>Account Settings</span>
        </a>
    </li>
    <li>
        <hr class="dropdown-divider">
    </li>

    <li>
        <a class="dropdown-item d-flex align-items-center" href="message-us.php">
            <i class="bi bi-question-circle"></i>
            <span>Need Help?</span>
        </a>
    </li>
    <li>
        <hr class="dropdown-divider">
    </li>

    <li>
        <a class="dropdown-item d-flex align-items-center" href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
        </a>
    </li>
</ul>
<!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header>
