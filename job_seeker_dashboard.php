<?php
session_start();
include 'common-header.php';
include 'config.php';
include 'header.php';
include 'sidenav.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to apply for a job.'); window.location.href='login.php';</script>";
    exit();
}

// Only enforce profile completion when arriving from Apply action
if (isset($_GET['apply'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name, dob, father_name, mother_name, address, languages_known, profile_summary, resume, resume_path FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $missingFields = [];
    if (empty(trim($user['name'] ?? ''))) { $missingFields[] = 'Full Name'; }
    if (empty(trim($user['dob'] ?? ''))) { $missingFields[] = 'Date of Birth'; }
    if (empty(trim($user['father_name'] ?? ''))) { $missingFields[] = "Father's Name"; }
    if (empty(trim($user['mother_name'] ?? ''))) { $missingFields[] = "Mother's Name"; }
    if (empty(trim($user['address'] ?? ''))) { $missingFields[] = 'Address'; }
    if (empty(trim($user['languages_known'] ?? ''))) { $missingFields[] = 'Languages Known'; }
    if (empty(trim($user['profile_summary'] ?? ''))) { $missingFields[] = 'Profile Summary'; }
    if (empty(trim($user['resume'] ?? '')) && empty(trim($user['resume_path'] ?? ''))) { $missingFields[] = 'Resume'; }

    if (!empty($missingFields)) {
        $fieldsText = implode(', ', $missingFields);
        echo "<script>alert('Please complete your profile before applying. Missing: {$fieldsText}.'); window.location.href='job-seeker-profile.php';</script>";
        exit();
    }
}

// Dashboard stats for job seeker
$userId = $_SESSION['user_id'];
$jobCount = (int) ($conn->query("SELECT COUNT(*) AS count FROM jobs")->fetch_assoc()['count'] ?? 0);

$appliedCountStmt = $conn->prepare("SELECT COUNT(*) AS count FROM job_applications WHERE user_id = ?");
$appliedCountStmt->bind_param("i", $userId);
$appliedCountStmt->execute();
$appliedCount = (int) ($appliedCountStmt->get_result()->fetch_assoc()['count'] ?? 0);
$appliedCountStmt->close();

$profileStmt = $conn->prepare("SELECT name, dob, father_name, mother_name, address, languages_known, profile_summary, resume, resume_path FROM users WHERE id = ?");
$profileStmt->bind_param("i", $userId);
$profileStmt->execute();
$profile = $profileStmt->get_result()->fetch_assoc();
$profileStmt->close();

$fieldsTotal = 8;
$fieldsFilled = 0;
if (!empty(trim($profile['name'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['dob'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['father_name'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['mother_name'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['address'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['languages_known'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['profile_summary'] ?? ''))) { $fieldsFilled++; }
if (!empty(trim($profile['resume'] ?? '')) || !empty(trim($profile['resume_path'] ?? ''))) { $fieldsFilled++; }
$profileScore = (int) round(($fieldsFilled / $fieldsTotal) * 100);
?>

<main id="main" class="main">
    <div class="container">
        <h2 class="text-center mb-4">Welcome to Your Dashboard</h2>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Jobs</h5>
                        <p class="card-text"><?= $jobCount ?> Jobs</p>
                        <a href="jobshow.php" class="btn btn-primary">View Jobs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Applied Jobs</h5>
                        <p class="card-text"><?= $appliedCount ?> Applied</p>
                        <a href="jobshow.php" class="btn btn-outline-primary">Explore</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Profile Score</h5>
                        <p class="card-text"><?= $profileScore ?>%</p>
                        <a href="job-seeker-profile.php" class="btn btn-outline-primary">Improve</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Interview & Resume Help</h5>
                        <p class="card-text">Boost your preparation</p>
                        <a href="interview-resume-help.php" class="btn btn-success">Open</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Essential Skills Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="text-center">Skills to Focus On</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Communication Skills:</strong> Improve your ability to convey ideas effectively in verbal and written formats.
                    </li>
                    <li class="list-group-item">
                        <strong>Technical Skills:</strong> Stay updated with industry-relevant tools, programming languages, or software.
                    </li>
                    <li class="list-group-item">
                        <strong>Problem-Solving Skills:</strong> Learn to analyze situations and develop creative solutions.
                    </li>
                    <li class="list-group-item">
                        <strong>Teamwork:</strong> Enhance your ability to collaborate and work effectively in teams.
                    </li>
                    <li class="list-group-item">
                        <strong>Leadership:</strong> Cultivate skills to lead projects and inspire teams.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
