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

// Fetch jobs posted by the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT jobs.id, jobs.title, jobs.type, jobs.location, jobs.salary, jobs.category, jobs.description, companies.name AS company_name FROM jobs INNER JOIN companies ON jobs.company_id = companies.id WHERE jobs.posted_by = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$jobs = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Manage Jobs</h2>

        <?php if ($jobs->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sno.</th>
                            <th>Job Title</th>
                            <th>Job Type</th>
                            <th>Company Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php while ($job = $jobs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++; ?></td>
                                <td><?= htmlspecialchars($job['title']); ?></td>
                                <td><?= htmlspecialchars($job['type']); ?></td>
                                <td><?= htmlspecialchars($job['company_name']); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm my-1" data-bs-toggle="modal" data-bs-target="#viewModal<?= $job['id']; ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm my-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $job['id']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm my-1" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $job['id']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?= $job['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $job['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel<?= $job['id']; ?>">Job Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Title:</strong> <?= htmlspecialchars($job['title']); ?></p>
                                                    <p><strong>Type:</strong> <?= htmlspecialchars($job['type']); ?></p>
                                                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']); ?></p>
                                                    <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary'] ?: 'N/A'); ?></p>
                                                    <p><strong>Category:</strong> <?= htmlspecialchars($job['category']); ?></p>
                                                    <p><strong>Description:</strong> <?= htmlspecialchars($job['description']); ?></p>
                                                    <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name']); ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $job['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $job['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?= $job['id']; ?>">Edit Job</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="edit-job.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $job['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="job_title">Job Title</label>
                                                            <input type="text" id="job_title" name="job_title" class="form-control" value="<?= htmlspecialchars($job['title']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="job_type">Job Type</label>
                                                            <input type="text" id="job_type" name="job_type" class="form-control" value="<?= htmlspecialchars($job['type']); ?>" disabled>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="location">Location</label>
                                                            <input type="text" id="location" name="location" class="form-control" value="<?= htmlspecialchars($job['location']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="salary">Salary</label>
                                                            <input type="text" id="salary" name="salary" class="form-control" value="<?= htmlspecialchars($job['salary']); ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="category">Category</label>
                                                            <input type="text" id="category" name="category" class="form-control" value="<?= htmlspecialchars($job['category']); ?>" disabled>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description">Description</label>
                                                            <textarea id="description" name="description" class="form-control" rows="5" required><?= htmlspecialchars($job['description']); ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="company_name">Company Name</label>
                                                            <input type="text" id="company_name" name="company_name" class="form-control" value="<?= htmlspecialchars($job['company_name']); ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?= $job['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $job['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?= $job['id']; ?>">Delete Job</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this job?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="delete-job.php?id=<?= $job['id']; ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No jobs found. <a href="post-job.php">Post a new job</a>.</p>
        <?php endif; ?>

        <?php $stmt->close(); ?>
        <?php $conn->close(); ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>