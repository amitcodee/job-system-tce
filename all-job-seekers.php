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

// Fetch all job seekers (active + deactive) who are not placed
$stmt = $conn->prepare(
    "SELECT u.id, u.name, u.email, u.phone, u.dob, u.father_name, u.mother_name, u.address, u.languages_known, u.profile_summary, u.resume, u.resume_path, u.created_at, u.role
     FROM users u
     LEFT JOIN placements p ON p.user_id = u.id
     WHERE u.role IN ('job_seeker','deactive') AND p.id IS NULL
     ORDER BY u.id DESC"
);
$stmt->execute();
$seekers = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Seekers</h2>

        <?php if ($seekers->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sno = 1; ?>
                        <?php while ($seeker = $seekers->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($seeker['name'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($seeker['email'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($seeker['phone'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($seeker['created_at'] ?? ''); ?></td>
                                <td>
                                    <?php if (($seeker['role'] ?? '') === 'deactive'): ?>
                                        <span class="badge bg-secondary">Deactive</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#seekerModal"
                                        data-name="<?= htmlspecialchars($seeker['name'] ?? ''); ?>"
                                        data-email="<?= htmlspecialchars($seeker['email'] ?? ''); ?>"
                                        data-phone="<?= htmlspecialchars($seeker['phone'] ?? ''); ?>"
                                        data-dob="<?= htmlspecialchars($seeker['dob'] ?? ''); ?>"
                                        data-father="<?= htmlspecialchars($seeker['father_name'] ?? ''); ?>"
                                        data-mother="<?= htmlspecialchars($seeker['mother_name'] ?? ''); ?>"
                                        data-address="<?= htmlspecialchars($seeker['address'] ?? ''); ?>"
                                        data-languages="<?= htmlspecialchars($seeker['languages_known'] ?? ''); ?>"
                                        data-summary="<?= htmlspecialchars($seeker['profile_summary'] ?? ''); ?>"
                                        data-resume="<?= htmlspecialchars($seeker['resume'] ?? ''); ?>"
                                        data-resume-path="<?= htmlspecialchars($seeker['resume_path'] ?? ''); ?>"
                                        data-joined="<?= htmlspecialchars($seeker['created_at'] ?? ''); ?>"
                                        data-status="<?= htmlspecialchars($seeker['role'] ?? ''); ?>">
                                        View
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#placementModal"
                                        data-user-id="<?= (int) $seeker['id']; ?>"
                                        data-user-name="<?= htmlspecialchars($seeker['name'] ?? ''); ?>">
                                        Placement
                                    </button>
                                    <?php if (($seeker['role'] ?? '') === 'deactive'): ?>
                                        <a class="btn btn-sm btn-success" href="activate-job-seeker.php?id=<?= (int) $seeker['id']; ?>" onclick="return confirm('Activate this user?');">Activate</a>
                                    <?php else: ?>
                                        <a class="btn btn-sm btn-warning" href="deactivate-job-seeker.php?id=<?= (int) $seeker['id']; ?>" onclick="return confirm('Deactivate this user?');">Deactivate</a>
                                    <?php endif; ?>
                                    <a class="btn btn-sm btn-danger" href="delete-job-seeker.php?id=<?= (int) $seeker['id']; ?>" onclick="return confirm('Delete this user permanently?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No job seekers found.</p>
        <?php endif; ?>

        <?php $stmt->close(); ?>
        <?php $conn->close(); ?>
    </div>

    <!-- Job Seeker Details Modal -->
    <div class="modal fade" id="seekerModal" tabindex="-1" aria-labelledby="seekerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seekerModalLabel">Job Seeker Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span id="modalSeekerName"></span></p>
                    <p><strong>Email:</strong> <span id="modalSeekerEmail"></span></p>
                    <p><strong>Phone:</strong> <span id="modalSeekerPhone"></span></p>
                    <p><strong>Date of Birth:</strong> <span id="modalSeekerDob"></span></p>
                    <p><strong>Father Name:</strong> <span id="modalSeekerFather"></span></p>
                    <p><strong>Mother Name:</strong> <span id="modalSeekerMother"></span></p>
                    <p><strong>Address:</strong> <span id="modalSeekerAddress"></span></p>
                    <p><strong>Languages Known:</strong> <span id="modalSeekerLanguages"></span></p>
                    <p><strong>Profile Summary:</strong></p>
                    <div class="border rounded p-2 mb-2" id="modalSeekerSummary"></div>
                    <p><strong>Resume:</strong> <span id="modalSeekerResume"></span></p>
                    <p><strong>Joined:</strong> <span id="modalSeekerJoined"></span></p>
                    <p><strong>Status:</strong> <span id="modalSeekerStatus"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Placement Modal -->
    <div class="modal fade" id="placementModal" tabindex="-1" aria-labelledby="placementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add-placement.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="placementModalLabel">Add Placement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="placementUserId">
                        <div class="mb-3">
                            <label class="form-label">Job Seeker</label>
                            <input type="text" class="form-control" id="placementUserName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile</label>
                            <input type="text" class="form-control" name="profile" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Placement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var seekerModal = document.getElementById('seekerModal');
            seekerModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                document.getElementById('modalSeekerName').textContent = button.getAttribute('data-name');
                document.getElementById('modalSeekerEmail').textContent = button.getAttribute('data-email');
                document.getElementById('modalSeekerPhone').textContent = button.getAttribute('data-phone');
                document.getElementById('modalSeekerDob').textContent = button.getAttribute('data-dob') || 'N/A';
                document.getElementById('modalSeekerFather').textContent = button.getAttribute('data-father') || 'N/A';
                document.getElementById('modalSeekerMother').textContent = button.getAttribute('data-mother') || 'N/A';
                document.getElementById('modalSeekerAddress').textContent = button.getAttribute('data-address') || 'N/A';
                document.getElementById('modalSeekerLanguages').textContent = button.getAttribute('data-languages') || 'N/A';
                var summary = button.getAttribute('data-summary');
                document.getElementById('modalSeekerSummary').textContent = summary || 'N/A';
                var resumePath = button.getAttribute('data-resume-path');
                var resumeName = button.getAttribute('data-resume');
                var resumeUrl = '';
                if (resumePath && resumePath !== '0') {
                    resumeUrl = resumePath;
                } else if (resumeName && /\.pdf$/i.test(resumeName)) {
                    resumeUrl = 'uploads/resumes/' + resumeName;
                }

                if (resumeUrl) {
                    document.getElementById('modalSeekerResume').innerHTML =
                        '<a href="' + resumeUrl + '" target="_blank">Open Resume</a>';
                } else if (resumeName) {
                    document.getElementById('modalSeekerResume').textContent = resumeName;
                } else {
                    document.getElementById('modalSeekerResume').textContent = 'N/A';
                }
                document.getElementById('modalSeekerJoined').textContent = button.getAttribute('data-joined');
                var status = button.getAttribute('data-status') === 'deactive' ? 'Deactive' : 'Active';
                document.getElementById('modalSeekerStatus').textContent = status;
            });

            var placementModal = document.getElementById('placementModal');
            placementModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                document.getElementById('placementUserId').value = button.getAttribute('data-user-id');
                document.getElementById('placementUserName').value = button.getAttribute('data-user-name');
            });
        });
    </script>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
