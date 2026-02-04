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

if ($userRole !== 'admin') {
    echo "<script>alert('You do not have permission to access this page.'); window.location.href='login.php';</script>";
    exit();
}

$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = isset($_POST['job_id']) ? (int)$_POST['job_id'] : 0;

    $jobTitle = trim($_POST['job_title'] ?? '');
    $jobType = trim($_POST['job_type'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $companyName = trim($_POST['company_name'] ?? '');
    $companyWebsite = trim($_POST['company_website'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $jobTypeManual = trim($_POST['job_type_manual'] ?? '');
    $categoryManual = trim($_POST['category_manual'] ?? '');

    if ($jobType === 'Other' && $jobTypeManual !== '') {
        $jobType = $jobTypeManual;
    }

    if ($category === 'Other' && $categoryManual !== '') {
        $category = $categoryManual;
    }

    if (empty($jobId) || empty($jobTitle) || empty($jobType) || empty($location) || empty($category) || empty($description) || empty($companyName)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    $updateStmt = $conn->prepare(
        "UPDATE jobs 
         SET title = ?, type = ?, location = ?, salary = ?, category = ?, company_name = ?, company_website = ?, description = ?
         WHERE id = ? AND posted_by = ?"
    );
    $updateStmt->bind_param(
        "ssssssssii",
        $jobTitle,
        $jobType,
        $location,
        $salary,
        $category,
        $companyName,
        $companyWebsite,
        $description,
        $jobId,
        $userId
    );

    if ($updateStmt->execute()) {
        echo "<script>alert('Job updated successfully.'); window.location.href='all-joblist.php';</script>";
        exit();
    }

    echo "<script>alert('Failed to update the job. Please try again.'); window.history.back();</script>";
    exit();
}

if (empty($jobId)) {
    echo "<script>alert('Invalid job ID.'); window.location.href='all-joblist.php';</script>";
    exit();
}

$jobStmt = $conn->prepare(
    "SELECT id, title, type, location, salary, category, company_name, company_website, description
     FROM jobs
     WHERE id = ? AND posted_by = ?"
);
$jobStmt->bind_param("ii", $jobId, $userId);
$jobStmt->execute();
$jobResult = $jobStmt->get_result();

if ($jobResult->num_rows === 0) {
    echo "<script>alert('Job not found or access denied.'); window.location.href='all-joblist.php';</script>";
    exit();
}

$job = $jobResult->fetch_assoc();
$jobStmt->close();

$jobTypeOptions = ['Full-Time', 'Part-Time', 'Freelance', 'Contract'];
$categoryOptions = ['Design', 'Development', 'AI', 'Marketing', 'Data Analysis', 'Cybersecurity', 'Project Management', 'Customer Support', 'Content Writing', 'HR & Recruitment'];

$isJobTypeCustom = !in_array($job['type'], $jobTypeOptions, true);
$isCategoryCustom = !in_array($job['category'], $categoryOptions, true);
?>

<main id="main" class="main">
    <div class="container">
        <h2>Edit Job (Admin)</h2>
        <p class="text-muted mb-4">Update job details and save changes.</p>
        <form action="" method="POST" enctype="multipart/form-data" class="row g-3" id="adminJobForm">
            <input type="hidden" name="job_id" value="<?= htmlspecialchars((string)$job['id']); ?>">
            <div class="col-12">
                <h5 class="mb-1">Job Basics</h5>
                <hr class="mt-2">
            </div>
            <div class="col-md-6">
                <label for="job_title" class="form-label">Job Title *</label>
                <input type="text" id="job_title" name="job_title" class="form-control" value="<?= htmlspecialchars($job['title'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
                <label for="job_type" class="form-label">Job Type *</label>
                <select id="job_type" name="job_type" class="form-select" required>
                    <option value="">Select Job Type</option>
                    <?php foreach ($jobTypeOptions as $option): ?>
                        <option value="<?= htmlspecialchars($option); ?>" <?= ($job['type'] === $option) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($option); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="Other" <?= $isJobTypeCustom ? 'selected' : ''; ?>>Other (Manual)</option>
                </select>
            </div>
            <div class="col-md-6 <?= $isJobTypeCustom ? '' : 'd-none'; ?>" id="job_type_manual_wrapper">
                <label for="job_type_manual" class="form-label">Manual Job Type *</label>
                <input type="text" id="job_type_manual" name="job_type_manual" class="form-control" value="<?= $isJobTypeCustom ? htmlspecialchars($job['type'] ?? '') : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="location" class="form-label">Location *</label>
                <input type="text" id="location" name="location" class="form-control" value="<?= htmlspecialchars($job['location'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
                <label for="salary" class="form-label">Salary (Optional)</label>
                <input type="text" id="salary" name="salary" class="form-control" value="<?= htmlspecialchars($job['salary'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label for="category" class="form-label">Category *</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categoryOptions as $option): ?>
                        <option value="<?= htmlspecialchars($option); ?>" <?= ($job['category'] === $option) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($option); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="Other" <?= $isCategoryCustom ? 'selected' : ''; ?>>Other (Manual)</option>
                </select>
            </div>
            <div class="col-md-6 <?= $isCategoryCustom ? '' : 'd-none'; ?>" id="category_manual_wrapper">
                <label for="category_manual" class="form-label">Manual Category *</label>
                <input type="text" id="category_manual" name="category_manual" class="form-control" value="<?= $isCategoryCustom ? htmlspecialchars($job['category'] ?? '') : ''; ?>">
            </div>
            <div class="col-12 mt-4">
                <h5 class="mb-1">Company Information</h5>
                <hr class="mt-2">
            </div>
            <div class="col-md-6">
                <label for="company_name" class="form-label">Company Name *</label>
                <input type="text" id="company_name" name="company_name" class="form-control" value="<?= htmlspecialchars($job['company_name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
                <label for="company_website" class="form-label">Company Website (Optional)</label>
                <input type="url" id="company_website" name="company_website" class="form-control" value="<?= htmlspecialchars($job['company_website'] ?? ''); ?>">
            </div>
            <div class="col-12 mt-4">
                <h5 class="mb-1">Job Description</h5>
                <hr class="mt-2">
            </div>
            <div class="col-md-12">
                <label for="description" class="form-label">Job Description *</label>
                <textarea id="description" name="description" class="form-control" rows="8"><?= htmlspecialchars($job['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Update Job</button>
                <a href="all-joblist.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>

<script src="https://cdn.tiny.cloud/1/5xliusgp4nerfnlfh5ao0o45fozqdxibc7nfkygy1c00uitl/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#description',
        height: 320,
        menubar: false,
        plugins: 'lists link table code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link table | removeformat | code'
    });

    const jobTypeSelect = document.getElementById('job_type');
    const jobTypeManualWrapper = document.getElementById('job_type_manual_wrapper');
    const jobTypeManualInput = document.getElementById('job_type_manual');

    const categorySelect = document.getElementById('category');
    const categoryManualWrapper = document.getElementById('category_manual_wrapper');
    const categoryManualInput = document.getElementById('category_manual');

    function toggleManualJobType() {
        const isManual = jobTypeSelect.value === 'Other';
        jobTypeManualWrapper.classList.toggle('d-none', !isManual);
        jobTypeManualInput.required = isManual;
        if (!isManual) {
            jobTypeManualInput.value = '';
        }
    }

    function toggleManualCategory() {
        const isManual = categorySelect.value === 'Other';
        categoryManualWrapper.classList.toggle('d-none', !isManual);
        categoryManualInput.required = isManual;
        if (!isManual) {
            categoryManualInput.value = '';
        }
    }

    jobTypeSelect.addEventListener('change', toggleManualJobType);
    categorySelect.addEventListener('change', toggleManualCategory);

    toggleManualJobType();
    toggleManualCategory();

    const adminJobForm = document.getElementById('adminJobForm');
    adminJobForm.addEventListener('submit', function (event) {
        const editor = tinymce.get('description');
        const text = editor ? editor.getContent({ format: 'text' }).trim() : '';
        if (!text) {
            event.preventDefault();
            alert('Please enter a job description.');
            if (editor) {
                editor.focus();
            }
            return;
        }
        if (editor) {
            editor.save();
        }
    });
</script>
