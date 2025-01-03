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

// Fetch the user's role from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Validate the user's role
if (!$user || $user['role'] !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch all companies from the database
$companiesStmt = $conn->prepare("SELECT id, name, type, email, phone, added_by, created_at FROM companies");
$companiesStmt->execute();
$companies = $companiesStmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>All Companies</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Added By</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($companies->num_rows > 0): ?>
                        <?php $counter = 1; ?>
                        <?php while ($company = $companies->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++; ?></td>
                                <td><?= htmlspecialchars($company['name']); ?></td>
                                <td><?= htmlspecialchars($company['type']); ?></td>
                                <td><?= htmlspecialchars($company['email']); ?></td>
                                <td><?= htmlspecialchars($company['phone']); ?></td>
                                <td>
                                    <?php
                                    $ownerStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                                    $ownerStmt->bind_param("i", $company['added_by']);
                                    $ownerStmt->execute();
                                    $ownerResult = $ownerStmt->get_result();
                                    $owner = $ownerResult->fetch_assoc();
                                    $ownerStmt->close();
                                    echo htmlspecialchars($owner['name'] ?? 'Unknown');
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($company['created_at']); ?></td>
                                <td>
                                    <a href="view-company.php?id=<?= $company['id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="delete-company.php?id=<?= $company['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this company?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No companies found.</td>
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
$companiesStmt->close();
$conn->close();
?>
