<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
?>

<main id="main" class="main">
    <div class="container">
        <h2>Manage Companies</h2>

        <?php
        include 'config.php';

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
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
            if ($role !== 'job_provider') {
                echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
            exit();
        }

        $stmt->close();

        // Fetch companies added by the logged-in user
        $stmt = $conn->prepare("SELECT id, name, type, email, phone, website, address, description, logo FROM companies WHERE added_by = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SNo.</th>
                            <th>Logo</th></>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++; ?></td>
                                <td>
                                    <?php if ($row['logo']): ?>
                                        <img src="<?= htmlspecialchars($row['logo']); ?>" alt="Company Logo" style="width: 50px; height: auto;">
                                    <?php else: ?>
                                        <!-- <img src="./uploads/company_logos/dummy.webp" alt="Company Logo" style="width: 50px; height: auto;"> -->
                                      <div class="ms-3">
                                      🖼️ 
                                      </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['type']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm my-1" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id']; ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm my-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="delete-company.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm my-1" onclick="return confirm('Are you sure you want to delete this company?');">
                                        <i class="bi bi-trash"></i>
                                    </a>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel<?= $row['id']; ?>">Company Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Name:</strong> <?= htmlspecialchars($row['name']); ?></p>
                                                    <p><strong>Type:</strong> <?= htmlspecialchars($row['type']); ?></p>
                                                    <p><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
                                                    <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']); ?></p>
                                                    <p><strong>Website:</strong> <a href="<?= htmlspecialchars($row['website']); ?>" target="_blank">Visit Website</a></p>
                                                    <p><strong>Address:</strong> <?= htmlspecialchars($row['address']); ?></p>
                                                    <p><strong>Description:</strong> <?= htmlspecialchars($row['description']); ?></p>
                                                    <?php if ($row['logo']): ?>
                                                        <p><strong>Logo:</strong></p>
                                                        <img src="<?= htmlspecialchars($row['logo']); ?>" alt="Company Logo" style="width: 100px; height: auto;">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?= $row['id']; ?>">Edit Company</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="update-company.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="name">Company Name *</label>
                                                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($row['name']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="type">Company Type *</label>
                                                                <input type="text" id="type" name="type" class="form-control" value="<?= htmlspecialchars($row['type']); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="email">Company Email *</label>
                                                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="phone">Company Phone *</label>
                                                                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone']); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="website">Company Website</label>
                                                                <input type="url" id="website" name="website" class="form-control" value="<?= htmlspecialchars($row['website']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="address">Company Address *</label>
                                                                <textarea id="address" name="address" class="form-control" required><?= htmlspecialchars($row['address']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="description">Company Description</label>
                                                                <textarea id="description" name="description" class="form-control"><?= htmlspecialchars($row['description']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="logo">Company Logo</label>
                                                            <input type="file" name="logo" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update Company</button>
                                                    </div>
                                                </form>
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
            <p>No companies found. <a href="add-company.php">Add a new company</a>.</p>
        <?php endif; ?>

        <?php $stmt->close(); ?>
        <?php $conn->close(); ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>