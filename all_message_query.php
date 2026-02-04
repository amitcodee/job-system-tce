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

// Fetch all messages
$stmt = $conn->prepare("SELECT id, name, email, subject, message, created_at FROM contact_messages ORDER BY created_at DESC");
$stmt->execute();
$messages = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>All Messages</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($messages->num_rows > 0): ?>
                        <?php $sno = 1; ?>
                        <?php while ($message = $messages->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($message['name']); ?></td>
                                <td><?= htmlspecialchars($message['email']); ?></td>
                                <td><?= htmlspecialchars($message['created_at']); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewMessageModal<?= $message['id']; ?>">View</button>
                                    <a href="delete-message.php?id=<?= $message['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                                </td>
                            </tr>

                            <!-- View Message Modal -->
                            <div class="modal fade" id="viewMessageModal<?= $message['id']; ?>" tabindex="-1" aria-labelledby="viewMessageModalLabel<?= $message['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewMessageModalLabel<?= $message['id']; ?>">Message Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Name:</strong> <?= htmlspecialchars($message['name']); ?></p>
                                            <p><strong>Email:</strong> <?= htmlspecialchars($message['email']); ?></p>
                                            <p><strong>Subject:</strong> <?= htmlspecialchars($message['subject'] ?? 'N/A'); ?></p>
                                            <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($message['message'] ?? 'N/A')); ?></p>
                                            <p><strong>Message Date:</strong> <?= htmlspecialchars($message['created_at']); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No messages found.</td>
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
