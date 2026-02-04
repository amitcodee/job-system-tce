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

// Fetch the user's role
$userId = $_SESSION['user_id'];
$roleStmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$roleStmt->bind_param("i", $userId);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
if ($roleResult->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit();
}
$userRole = $roleResult->fetch_assoc()['role'];
$roleStmt->close();

// Restrict access to admin only
if ($userRole !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch applications for jobs posted by this admin
$appStmt = $conn->prepare(
    "SELECT ja.id, ja.applied_at, j.id AS job_id, j.title AS job_title,
            u.id AS user_id, u.name, u.email, u.phone, u.resume, u.resume_path
     FROM job_applications ja
     INNER JOIN jobs j ON ja.job_id = j.id
     INNER JOIN users u ON ja.user_id = u.id
     WHERE j.posted_by = ?
     ORDER BY ja.applied_at DESC"
);
$appStmt->bind_param("i", $userId);
$appStmt->execute();
$applications = $appStmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0">Job Applications</h2>
        </div>

        <?php if ($applications->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Job Title</th>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Resume</th>
                            <th>Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sno = 1; ?>
                        <?php while ($row = $applications->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td>
                                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#jobModal" 
                                       data-job-id="<?= $row['job_id']; ?>"
                                       data-job-title="<?= htmlspecialchars($row['job_title'] ?? ''); ?>">
                                        <?= htmlspecialchars($row['job_title'] ?? ''); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#userModal" 
                                       data-user-id="<?= $row['user_id']; ?>"
                                       data-user-name="<?= htmlspecialchars($row['name'] ?? ''); ?>"
                                       data-user-email="<?= htmlspecialchars($row['email'] ?? ''); ?>"
                                       data-user-phone="<?= htmlspecialchars($row['phone'] ?? ''); ?>">
                                        <?= htmlspecialchars($row['name'] ?? ''); ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['email'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['phone'] ?? ''); ?></td>
                                <td>
                                    <?php
                                        $resumePath = !empty($row['resume_path']) && $row['resume_path'] !== '0' ? $row['resume_path'] : '';
                                        $resumeName = !empty($row['resume']) ? $row['resume'] : '';
                                        if (empty($resumePath) && !empty($resumeName) && preg_match('/\.pdf$/i', $resumeName)) {
                                            $resumePath = 'uploads/resumes/' . $resumeName;
                                        }
                                    ?>
                                    <?php if (!empty($resumePath)): ?>
                                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#resumeModal" 
                                           data-resume-path="<?= htmlspecialchars($resumePath); ?>"
                                           data-user-name="<?= htmlspecialchars($row['name'] ?? ''); ?>">
                                            View
                                        </a>
                                    <?php elseif (!empty($resumeName)): ?>
                                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#resumeModal" 
                                           data-resume-text="<?= htmlspecialchars($resumeName); ?>"
                                           data-user-name="<?= htmlspecialchars($row['name'] ?? ''); ?>">
                                            View
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['applied_at'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No applications found for your jobs.</p>
        <?php endif; ?>

        <?php $appStmt->close(); ?>
        <?php $conn->close(); ?>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Applicant Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Name:</strong> <span id="modalUserName"></span></p>
                            <p><strong>Email:</strong> <span id="modalUserEmail"></span></p>
                            <p><strong>Phone:</strong> <span id="modalUserPhone"></span></p>
                            <p><strong>User ID:</strong> <span id="modalUserId"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalLabel">Job Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jobDetailsContent">
                        <p class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resume Modal -->
    <div class="modal fade" id="resumeModal" tabindex="-1" aria-labelledby="resumeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resumeModalLabel">Resume - <span id="modalResumeName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="resumeDownloadWrap" class="text-center mb-3">
                        <a id="resumeDownloadLink" href="#" target="_blank" class="btn btn-primary">
                            <i class="bi bi-download"></i> Download Resume
                        </a>
                    </div>
                    <iframe id="resumeFrame" style="width:100%; height:600px; border:1px solid #ddd;" src=""></iframe>
                    <div id="resumeText" class="border rounded p-3" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // User Modal
        document.addEventListener('DOMContentLoaded', function() {
            var userModal = document.getElementById('userModal');
            userModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var userName = button.getAttribute('data-user-name');
                var userEmail = button.getAttribute('data-user-email');
                var userPhone = button.getAttribute('data-user-phone');
                var userId = button.getAttribute('data-user-id');
                
                document.getElementById('modalUserName').textContent = userName;
                document.getElementById('modalUserEmail').textContent = userEmail;
                document.getElementById('modalUserPhone').textContent = userPhone;
                document.getElementById('modalUserId').textContent = userId;
            });

            // Job Modal
            var jobModal = document.getElementById('jobModal');
            jobModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var jobId = button.getAttribute('data-job-id');
                
                // Fetch job details via AJAX
                fetch('get-job-details.php?job_id=' + jobId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            var html = '<div class="job-details">';
                            html += '<p><strong>Job Title:</strong> ' + data.job.title + '</p>';
                            html += '<p><strong>Company:</strong> ' + data.job.company + '</p>';
                            html += '<p><strong>Location:</strong> ' + data.job.location + '</p>';
                            html += '<p><strong>Salary:</strong> ' + data.job.salary + '</p>';
                            html += '<p><strong>Description:</strong></p>';
                            html += '<p>' + data.job.description + '</p>';
                            html += '<p><strong>Requirements:</strong></p>';
                            html += '<p>' + data.job.requirements + '</p>';
                            html += '<p><strong>Posted On:</strong> ' + data.job.created_at + '</p>';
                            html += '</div>';
                            document.getElementById('jobDetailsContent').innerHTML = html;
                        } else {
                            document.getElementById('jobDetailsContent').innerHTML = '<p class="text-danger">Failed to load job details.</p>';
                        }
                    })
                    .catch(error => {
                        document.getElementById('jobDetailsContent').innerHTML = '<p class="text-danger">Error loading job details.</p>';
                    });
            });

            // Resume Modal
            var resumeModal = document.getElementById('resumeModal');
            resumeModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var resumePath = button.getAttribute('data-resume-path');
                var resumeText = button.getAttribute('data-resume-text');
                var userName = button.getAttribute('data-user-name');

                document.getElementById('modalResumeName').textContent = userName;

                if (resumePath) {
                    document.getElementById('resumeFrame').style.display = 'block';
                    document.getElementById('resumeFrame').src = resumePath;
                    document.getElementById('resumeDownloadWrap').style.display = 'block';
                    document.getElementById('resumeDownloadLink').href = resumePath;
                    document.getElementById('resumeText').style.display = 'none';
                    document.getElementById('resumeText').textContent = '';
                } else {
                    document.getElementById('resumeFrame').style.display = 'none';
                    document.getElementById('resumeFrame').src = '';
                    document.getElementById('resumeDownloadWrap').style.display = 'none';
                    document.getElementById('resumeDownloadLink').href = '#';
                    document.getElementById('resumeText').style.display = 'block';
                    document.getElementById('resumeText').textContent = resumeText || 'No resume details available.';
                }
            });
        });
    </script>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
