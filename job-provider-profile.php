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

$userId = $_SESSION['user_id'];

// Fetch provider details
$stmt = $conn->prepare("SELECT name, email, phone, position, profile_description FROM users WHERE id = ? AND role = 'job_provider'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<script>alert('Job provider not found.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch companies created by the job provider
$companyStmt = $conn->prepare("SELECT id, name FROM companies WHERE added_by = ?");
$companyStmt->bind_param("i", $userId);
$companyStmt->execute();
$companies = $companyStmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Edit Job Provider Profile</h2>

        <form action="update-job-provider-profile.php" method="POST">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">Edit Personal Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label"><strong>Name:</strong></label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><strong>Email:</strong></label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label"><strong>Phone:</strong></label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label"><strong>Position:</strong></label>
                        <input type="text" id="position" name="position" class="form-control" value="<?= htmlspecialchars($user['position'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="profile_description" class="form-label"><strong>Profile Description:</strong></label>
                        <textarea id="profile_description" name="profile_description" class="form-control" rows="4"><?= htmlspecialchars($user['profile_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

           

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>