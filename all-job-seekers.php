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

// Fetch the role of the logged-in user from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch all job seekers from the database
$stmt = $conn->prepare("SELECT id, name, email, phone, verified FROM users WHERE role = 'job_seeker'");
$stmt->execute();
$jobSeekers = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>All Job Seekers</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jobSeekers->num_rows > 0): ?>
                        <?php $counter = 1; ?>
                        <?php while ($seeker = $jobSeekers->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++; ?></td>
                                <td><?= htmlspecialchars($seeker['name']); ?></td>
                                <td><?= htmlspecialchars($seeker['email']); ?></td>
                                <td><?= htmlspecialchars($seeker['phone']); ?></td>
                                <td>
                                    <a href="view-job-seeker.php?id=<?= $seeker['id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="delete-job-seeker.php?id=<?= $seeker['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job seeker?');">Delete</a>
                                    <button 
                                        class="btn btn-sm <?= $seeker['verified'] ? 'btn-success' : 'btn-secondary'; ?>" 
                                        onclick="verifyJobSeeker(<?= $seeker['id']; ?>, this)">
                                        <?= $seeker['verified'] ? 'Verified' : 'Verify'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No job seekers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function verifyJobSeeker(seekerId, button) {
    fetch('verify-job-seeker.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: seekerId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.remove('btn-secondary');
            button.classList.add('btn-success');
            button.textContent = 'Verified';
        } else {
            alert(data.message || 'Failed to verify the job seeker.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while verifying the job seeker.');
    });
}
</script>

<?php
include 'footer.php';
include 'common-footer.php';
?>
