<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Fetch companies added by the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name FROM companies WHERE added_by = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$companies = $stmt->get_result();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Post a Job</h2>
        <form action="post-job-submit.php" method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label for="job_title" class="form-label">Job Title *</label>
                <input type="text" id="job_title" name="job_title" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="job_type" class="form-label">Job Type *</label>
                <select id="job_type" name="job_type" class="form-select" required>
                    <option value="">Select Job Type</option>
                    <option value="Full-Time">Full-Time</option>
                    <option value="Part-Time">Part-Time</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Contract">Contract</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="location" class="form-label">Location *</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="salary" class="form-label">Salary (Optional)</label>
                <input type="text" id="salary" name="salary" class="form-control">
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
                <label for="company_id" class="form-label">Select Company *</label>
                <select id="company_id" name="company_id" class="form-select" required>
                    <option value="">Select Company</option>
                    <?php while ($company = $companies->fetch_assoc()): ?>
                        <option value="<?= $company['id'] ?>"><?= htmlspecialchars($company['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-12">
                <label for="description" class="form-label">Job Description *</label>
                <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Post Job</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</main>

<?php 
include 'footer.php';
include 'common-footer.php';
?>