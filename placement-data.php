<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Fetch job seekers and their placement status
$jobSeekerQuery = "
    SELECT u.id, u.name, u.father_name, u.phone, 
           p.position, p.company_name, p.description, p.join_letter 
    FROM users u 
    LEFT JOIN placements p ON u.id = p.user_id 
    WHERE u.role = 'job_seeker'";
$jobSeekerResult = $conn->query($jobSeekerQuery);
?>

<main id="main" class="main">
    <div class="container">
        <h2>Placement Data</h2>

        <!-- Job Seekers Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Father's Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jobSeekerResult->num_rows > 0): ?>
                        <?php $sno = 1; ?>
                        <?php while ($row = $jobSeekerResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td> <a href="view-job-seeker.php?id=<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></a></td>
                               
                                <td><?= htmlspecialchars($row['father_name']); ?></td>
                                <td><?= htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <?= $row['position'] ? '<span class="badge bg-success">Placed</span>' : '<span class="badge bg-danger">Not Placed</span>'; ?>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id']; ?>">View</button>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#placedModal<?= $row['id']; ?>">Placed</button>
                                    <?php if ($row['position']): ?>
                                        <form action="unplace-job-seeker.php" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to unplace this job seeker?');">Unplaced</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Modal for View -->
                            <div class="modal fade" id="viewModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $row['id']; ?>">Job Seeker Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Personal Information</h5>
                                            <p><strong>Name:</strong> <?= htmlspecialchars($row['name']); ?></p>
                                            <p><strong>Father's Name:</strong> <?= htmlspecialchars($row['father_name']); ?></p>
                                            <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']); ?></p>

                                            <h5>Placement Information</h5>
                                            <?php if ($row['position']): ?>
                                                <p><strong>Position:</strong> <?= htmlspecialchars($row['position']); ?></p>
                                                <p><strong>Company Name:</strong> <?= htmlspecialchars($row['company_name']); ?></p>
                                                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($row['description'])); ?></p>
                                                <p><strong>Joining Letter:</strong> 
                                                    <a href="uploads/joining_letters/<?= htmlspecialchars($row['join_letter']); ?>" target="_blank">View Letter</a>
                                                </p>
                                            <?php else: ?>
                                                <p>No placement data available.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for Placed -->
                            <div class="modal fade" id="placedModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="placedModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="placedModalLabel<?= $row['id']; ?>">Placement Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="save-placement.php" method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?= $row['id']; ?>">

                                                <div class="mb-3">
                                                    <label for="position<?= $row['id']; ?>" class="form-label">Position *</label>
                                                    <input type="text" id="position<?= $row['id']; ?>" name="position" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="company_name<?= $row['id']; ?>" class="form-label">Company Name *</label>
                                                    <input type="text" id="company_name<?= $row['id']; ?>" name="company_name" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description<?= $row['id']; ?>" class="form-label">Description</label>
                                                    <textarea id="description<?= $row['id']; ?>" name="description" class="form-control" rows="4"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="join_letter<?= $row['id']; ?>" class="form-label">Upload Joining Letter *</label>
                                                    <input type="file" id="join_letter<?= $row['id']; ?>" name="join_letter" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No job seekers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
