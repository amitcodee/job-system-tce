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

?>

<main id="main" class="main">
    <div class="container">
        <h2>Add Job (Admin)</h2>
        <p class="text-muted mb-4">Enter complete job details. A clear title, location, and rich description help job seekers understand the role faster.</p>
        <form action="post-job-submit.php" method="POST" enctype="multipart/form-data" class="row g-3" id="adminJobForm">
            <div class="col-12">
                <h5 class="mb-1">Job Basics</h5>
                <hr class="mt-2">
            </div>
            <div class="col-md-6">
                <label for="job_title" class="form-label">Job Title *</label>
                <input type="text" id="job_title" name="job_title" class="form-control" required>
                <div class="form-text">Example: Junior Web Developer</div>
            </div>
            <div class="col-md-6">
                <label for="job_type" class="form-label">Job Type *</label>
                <select id="job_type" name="job_type" class="form-select" required>
                    <option value="">Select Job Type</option>
                    <option value="Full-Time">Full-Time</option>
                    <option value="Part-Time">Part-Time</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Contract">Contract</option>
                    <option value="Other">Other (Manual)</option>
                </select>
            </div>
            <div class="col-md-6 d-none" id="job_type_manual_wrapper">
                <label for="job_type_manual" class="form-label">Manual Job Type *</label>
                <input type="text" id="job_type_manual" name="job_type_manual" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="location" class="form-label">Location *</label>
                <input type="text" id="location" name="location" class="form-control" required>
                <div class="form-text">City, State (or Remote)</div>
            </div>
            <div class="col-md-6">
                <label for="salary" class="form-label">Salary (Optional)</label>
                <input type="text" id="salary" name="salary" class="form-control">
                <div class="form-text">Example: 25,000 - 35,000 per month</div>
            </div>
            <div class="col-md-6">
                <label for="category" class="form-label">Category *</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <option value="Design">Design</option>
                    <option value="Development">Development</option>
                    <option value="AI">AI</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Data Analysis">Data Analysis</option>
                    <option value="Cybersecurity">Cybersecurity</option>
                    <option value="Project Management">Project Management</option>
                    <option value="Customer Support">Customer Support</option>
                    <option value="Content Writing">Content Writing</option>
                    <option value="HR & Recruitment">HR & Recruitment</option>
                    <option value="Other">Other (Manual)</option>
                </select>
            </div>
            <div class="col-md-6 d-none" id="category_manual_wrapper">
                <label for="category_manual" class="form-label">Manual Category *</label>
                <input type="text" id="category_manual" name="category_manual" class="form-control">
            </div>
            <div class="col-12 mt-4">
                <h5 class="mb-1">Company Information</h5>
                <hr class="mt-2">
            </div>
            <div class="col-md-6">
                <label for="company_name" class="form-label">Company Name *</label>
                <input type="text" id="company_name" name="company_name" class="form-control" required>
                <div class="form-text">Enter the company name exactly as it should appear.</div>
            </div>
            <div class="col-md-6">
                <label for="company_website" class="form-label">Company Website (Optional)</label>
                <input type="url" id="company_website" name="company_website" class="form-control" placeholder="https://example.com">
            </div>
            <div class="col-12 mt-4">
                <h5 class="mb-1">Job Description</h5>
                <hr class="mt-2">
            </div>
            <div class="col-md-12">
                <label for="description" class="form-label">Job Description *</label>
                <textarea id="description" name="description" class="form-control" rows="8"></textarea>
                <div class="form-text">Include responsibilities, qualifications, and any benefits.</div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save Job</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
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
