<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Everything You Need to Succeed</h2>
                <p class="text-muted">
                    Our AI-powered platform provides all the tools you need to prepare for technical interviews and land your dream job.
                </p>

                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>GitHub Repository Analysis</strong><br>Automatically analyze any public GitHub repository to understand its structure, tech stack, and architecture.</li>
                            <li class="list-group-item"><strong>AI-Powered Questions</strong><br>Generate interview questions specifically based on the actual code in your repositories.</li>
                            <li class="list-group-item"><strong>Project-Scoped AI Chat</strong><br>Ask questions about any repository and get answers grounded only in the actual codebase.</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>ATS Resume Checker</strong><br>Check your resume's ATS compatibility with detailed scoring and keyword analysis.</li>
                            <li class="list-group-item"><strong>AI Resume Builder</strong><br>Build ATS-optimized resumes with 8 role-specific templates tailored for tech roles.</li>
                            <li class="list-group-item"><strong>Progress Tracking</strong><br>Track your interview preparation progress with detailed analytics and insights.</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="https://interviewprep.techcadd.com/" target="_blank" class="btn btn-primary btn-lg">
                        Open Interview Prep Platform
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
