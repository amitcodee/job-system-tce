<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view this page.'); window.location.href='login.php';</script>";
    exit();
}

// Validate the company ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid company ID.'); window.location.href='all-companies.php';</script>";
    exit();
}

$companyId = intval($_GET['id']);

// Fetch company details
$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$company) {
    echo "<script>alert('Company not found.'); window.location.href='all-companies.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">


        <!-- Company Information -->
        <div class="card mb-4">
            <div class="card-header">Company Information</div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-12 text-center">
                        <!-- logo -->
                        <?php if (!empty($company['logo']) && file_exists($company['logo'])): ?>
                            <img src="<?= htmlspecialchars($company['logo']); ?>" alt="Company Logo" class="img-fluid" style="max-width: 200px;">
                        <?php else: ?>
                            <p>No logo uploaded.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <p><strong>Name:</strong> <?= htmlspecialchars($company['name']); ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($company['type']); ?></p>
                <p><strong>Website:</strong>
                    <?php if (!empty($company['website'])): ?>
                        <a href="<?= htmlspecialchars($company['website']); ?>" target="_blank"><?= htmlspecialchars($company['website']); ?></a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </p>
                <p><strong>Email:</strong> <?= htmlspecialchars($company['email']); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($company['phone']); ?></p>
                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($company['address'])); ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($company['description'] ?? 'N/A')); ?></p>
                <?php
                // Fetch the username of the user who added the company
                $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                $stmt->bind_param("i", $company['added_by']);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                ?>
                <p><strong>Added By:</strong> <?= htmlspecialchars($user['name']); ?></p>
                <p><strong>Created At:</strong> <?= htmlspecialchars($company['created_at']); ?></p>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>