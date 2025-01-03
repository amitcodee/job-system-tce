<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Fetch filters for categories
$categories = [];

// Fetch distinct categories from jobs and job_preferences
$categoryStmt = $conn->query("SELECT DISTINCT category FROM (SELECT category FROM jobs UNION SELECT category FROM job_preferences) AS combined_categories");
while ($category = $categoryStmt->fetch_assoc()) {
    $categories[] = $category['category'];
}

// Apply filters
$filterCategory = isset($_GET['category']) ? trim($_GET['category']) : null;

// Fetch jobs by job providers with provider name
$jobQuery = "
    SELECT jobs.*, users.name AS provider_name 
    FROM jobs 
    JOIN users ON jobs.posted_by = users.id 
    WHERE users.role = 'job_provider'
";
if ($filterCategory) {
    $jobQuery .= " AND jobs.category = ?";
}
$jobStmt = $conn->prepare($jobQuery);
if ($filterCategory) {
    $jobStmt->bind_param("s", $filterCategory);
}
$jobStmt->execute();
$jobResults = $jobStmt->get_result();

// Fetch job preferences by job seekers with seeker name
$preferenceQuery = "
    SELECT job_preferences.*, users.name AS seeker_name 
    FROM job_preferences 
    JOIN users ON job_preferences.user_id = users.id 
    WHERE users.role = 'job_seeker'
";
if ($filterCategory) {
    $preferenceQuery .= " AND job_preferences.category = ?";
}
$preferenceStmt = $conn->prepare($preferenceQuery);
if ($filterCategory) {
    $preferenceStmt->bind_param("s", $filterCategory);
}
$preferenceStmt->execute();
$preferenceResults = $preferenceStmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Match</h2>

        <!-- Filter Section -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <select name="category" class="form-select">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category); ?>" <?= $filterCategory === $category ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="row">
            <!-- Jobs by Job Providers -->
            <div class="col-md-6">
                <h3>Jobs by Job Providers</h3>
                <div class="list-group">
                    <?php if ($jobResults->num_rows > 0): ?>
                        <?php while ($job = $jobResults->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <strong><?= htmlspecialchars($job['title']); ?></strong>
                                <p>Category: <?= htmlspecialchars($job['category']); ?></p>
                                <p>Location: <?= htmlspecialchars($job['location']); ?></p>
                                <p>Salary: <?= htmlspecialchars($job['salary'] ?? 'N/A'); ?></p>
                                <p>Provider: <?= htmlspecialchars($job['provider_name']); ?></p>
                                <a href="view-job.php?id=<?= $job['id']; ?>" class="btn btn-info btn-sm mt-2">More Details</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No jobs available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Job Preferences by Job Seekers -->
            <div class="col-md-6">
                <h3>Job Preferences by Job Seekers</h3>
                <div class="list-group">
                    <?php if ($preferenceResults->num_rows > 0): ?>
                        <?php while ($preference = $preferenceResults->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <strong><?= htmlspecialchars($preference['job_title']); ?></strong>
                                <p>Category: <?= htmlspecialchars($preference['category']); ?></p>
                                <p>Preferred Location: <?= htmlspecialchars($preference['location']); ?></p>
                                <p>Salary Expectation: <?= htmlspecialchars($preference['salary_expectation'] ?? 'N/A'); ?></p>
                                <p>Seeker: <?= htmlspecialchars($preference['seeker_name']); ?></p>
                                <a href="view-job-seeker.php?id=<?= $preference['user_id']; ?>" class="btn btn-info btn-sm mt-2">More Details</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No job preferences available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
