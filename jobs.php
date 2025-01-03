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

// Fetch existing job preferences for the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM job_preferences WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$preferences = $stmt->get_result();
$preferencesCount = $preferences->num_rows;

$canAddMorePreferences = $preferencesCount < 4;
?>

<main id="main" class="main">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Job Preferences</h2>
            <?php if ($canAddMorePreferences): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPreferenceModal">Add Job Preference</button>
            <?php endif; ?>
        </div>
        <p>You can create up to 4 job preferences.</p>

        <!-- Existing Preferences -->
        <?php if ($preferencesCount > 0): ?>
            <div class="mb-4">
                <h4>Your Job Preferences</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Job Title</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            <?php while ($preference = $preferences->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $counter++; ?></td>
                                    <td><?= htmlspecialchars($preference['job_title']); ?></td>
                                    <td><?= htmlspecialchars($preference['category']); ?></td>
                                    <td><?= htmlspecialchars($preference['location']); ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm"data-bs-toggle="modal" data-bs-target="#viewPreferenceModal<?= $preference['id']; ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="delete-job-preference.php?id=<?= $preference['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this preference?');">
                                            <i class="bi bi-trash"></i>
                                        </a>

                                        <!-- View Preference Modal -->
                                        <div class="modal fade" id="viewPreferenceModal<?= $preference['id']; ?>" tabindex="-1" aria-labelledby="viewPreferenceModalLabel<?= $preference['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewPreferenceModalLabel<?= $preference['id']; ?>">Job Preference Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Job Title:</strong> <?= htmlspecialchars($preference['job_title']); ?></p>
                                                        <p><strong>Category:</strong> <?= htmlspecialchars($preference['category']); ?></p>
                                                        <p><strong>Location:</strong> <?= htmlspecialchars($preference['location']); ?></p>
                                                        <p><strong>Salary Expectation:</strong> <?= htmlspecialchars($preference['salary_expectation'] ?? 'N/A'); ?></p>
                                                        <p><strong>Additional Notes:</strong> <?= htmlspecialchars($preference['additional_notes'] ?? 'N/A'); ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            </div>
        <?php else: ?>
            <p>You have not added any job preferences yet.</p>
        <?php endif; ?>

        <!-- Add Preference Modal -->
        <div class="modal fade" id="addPreferenceModal" tabindex="-1" aria-labelledby="addPreferenceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPreferenceModalLabel">Add Job Preference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="job-preferences-submit.php" method="POST" class="row g-3 p-3">
                        <div class="col-md-6">
                            <label for="job_title" class="form-label">Job Title *</label>
                            <input type="text" id="job_title" name="job_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category *</label>
                            <select id="category" name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Design" title="Technologies: Adobe Photoshop, Figma, Sketch">Design</option>
                                <option value="Development" title="Technologies: JavaScript, Python, Java">Development</option>
                                <option value="AI" title="Technologies: TensorFlow, PyTorch, OpenAI">AI</option>
                                <option value="Marketing" title="Technologies: Google Analytics, SEO Tools">Marketing</option>
                                <option value="Data Analysis" title="Technologies: Excel, Power BI, Tableau">Data Analysis</option>
                                <option value="Cybersecurity" title="Technologies: Firewalls, Ethical Hacking">Cybersecurity</option>
                                <option value="Project Management" title="Technologies: Jira, Trello, Agile">Project Management</option>
                                <option value="Customer Support" title="Technologies: CRM, Help Desk">Customer Support</option>
                                <option value="Content Writing" title="Technologies: Grammarly, WordPress">Content Writing</option>
                                <option value="HR & Recruitment" title="Technologies: ATS, LinkedIn">HR & Recruitment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Preferred Location *</label>
                            <input type="text" id="location" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="salary_expectation" class="form-label">Salary Expectation (Optional)</label>
                            <input type="text" id="salary_expectation" name="salary_expectation" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="additional_notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea id="additional_notes" name="additional_notes" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save Preferences</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (!$canAddMorePreferences): ?>
            <p class="text-danger">You have reached the maximum number of job preferences.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>