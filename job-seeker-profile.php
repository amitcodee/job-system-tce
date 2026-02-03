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

// Fetch user details
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Seeker Profile</h2>

        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">Personal Information</div>
            <div class="card-body">
                <form id="personalInfoForm" action="update-personal-info.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label"><strong>Full Name:</strong></label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label"><strong>Date of Birth:</strong></label>
                                <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="father_name" class="form-label"><strong>Father's Name:</strong></label>
                                <input type="text" id="father_name" name="father_name" class="form-control" value="<?= htmlspecialchars($user['father_name'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="mother_name" class="form-label"><strong>Mother's Name:</strong></label>
                                <input type="text" id="mother_name" name="mother_name" class="form-control" value="<?= htmlspecialchars($user['mother_name'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label"><strong>Address:</strong></label>
                                <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($user['address'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><strong>Email:</strong></label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label"><strong>Phone:</strong></label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="languages_known" class="form-label"><strong>Languages Known:</strong></label>
                                <input type="text" id="languages_known" name="languages_known" class="form-control" value="<?= htmlspecialchars($user['languages_known'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="profile_summary" class="form-label"><strong>Profile Summary:</strong></label>
                        <textarea id="profile_summary" name="profile_summary" class="form-control" rows="4"><?= htmlspecialchars($user['profile_summary'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resume" class="form-label"><strong>Upload Resume:</strong></label>
                        <input type="file" id="resume" name="resume" class="form-control">
                        <?php if (!empty($user['resume'])): ?>
                            <p class="mt-2">Current Resume: <a href="uploads/resumes/<?= htmlspecialchars($user['resume']); ?>" target="_blank">View Resume</a></p>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

            </div>
        </div>
    </div>
</main>
<?php
include 'footer.php';
include 'common-footer.php';
?>