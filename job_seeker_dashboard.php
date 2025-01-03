<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
?>

<main id="main" class="main">
    <div class="container">
        <h2 class="text-center mb-4">Welcome to Your Dashboard</h2>
        <!-- Essential Skills Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="text-center">Skills to Focus On</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Communication Skills:</strong> Improve your ability to convey ideas effectively in verbal and written formats.
                    </li>
                    <li class="list-group-item">
                        <strong>Technical Skills:</strong> Stay updated with industry-relevant tools, programming languages, or software.
                    </li>
                    <li class="list-group-item">
                        <strong>Problem-Solving Skills:</strong> Learn to analyze situations and develop creative solutions.
                    </li>
                    <li class="list-group-item">
                        <strong>Teamwork:</strong> Enhance your ability to collaborate and work effectively in teams.
                    </li>
                    <li class="list-group-item">
                        <strong>Leadership:</strong> Cultivate skills to lead projects and inspire teams.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>
