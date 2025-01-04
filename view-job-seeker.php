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

// Validate ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid user ID.'); window.location.href='all-job-seekers.php';</script>";
    exit();
}

$seekerId = intval($_GET['id']);

// Fetch personal details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'job_seeker'");
$stmt->bind_param("i", $seekerId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<script>alert('Job seeker not found.'); window.location.href='all-job-seekers.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Seeker Profile</h2>

        <!-- Personal Information -->
        <div class="card mb-4">
    <div class="card-header">Personal Information</div>
    <div class="card-body">
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob'] ?? 'N/A'); ?></p>
        <p><strong>Father's Name:</strong> <?= htmlspecialchars($user['father_name'] ?? 'N/A'); ?></p>
        <p><strong>Mother's Name:</strong> <?= htmlspecialchars($user['mother_name'] ?? 'N/A'); ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address'] ?? 'N/A'); ?></p>
        <p><strong>Languages Known:</strong> <?= htmlspecialchars($user['languages_known'] ?? 'N/A'); ?></p>
        <p><strong>Profile Summary:</strong> <?= nl2br(htmlspecialchars($user['profile_summary'] ?? 'N/A')); ?></p>
        <?php if (!empty($user['resume'])): ?>
            <p><strong>Resume:</strong> <a href="uploads/resumes/<?= htmlspecialchars($user['resume']); ?>" target="_blank">View Resume</a></p>
        <?php else: ?>
            <p><strong>Resume:</strong> Not uploaded</p>
        <?php endif; ?>
    </div>
</div>



        <!-- Skills -->
        <div class="card mb-4">
            <div class="card-header">Skills</div>
            <div class="card-body">
                <ul>
                    <?php
                    $skillsStmt = $conn->prepare("SELECT * FROM skills WHERE user_id = ?");
                    $skillsStmt->bind_param("i", $seekerId);
                    $skillsStmt->execute();
                    $skillsResult = $skillsStmt->get_result();
                    if ($skillsResult->num_rows > 0) {
                        while ($skill = $skillsResult->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($skill['skill_name']) . " (" . htmlspecialchars($skill['proficiency_level']) . ")</li>";
                        }
                    } else {
                        echo "<p>No skills added.</p>";
                    }
                    $skillsStmt->close();
                    ?>
                </ul>
            </div>
        </div>

        <!-- Education -->
        <div class="card mb-4">
            <div class="card-header">Education</div>
            <div class="card-body">
                <ul>
                    <?php
                    $educationStmt = $conn->prepare("SELECT * FROM education WHERE user_id = ?");
                    $educationStmt->bind_param("i", $seekerId);
                    $educationStmt->execute();
                    $educationResult = $educationStmt->get_result();
                    if ($educationResult->num_rows > 0) {
                        while ($edu = $educationResult->fetch_assoc()) {
                            $eduId = htmlspecialchars($edu['id']);
                            $degree = htmlspecialchars($edu['degree']);
                            $institution = htmlspecialchars($edu['institution']);
                            $fieldOfStudy = htmlspecialchars($edu['field_of_study']);
                            $startDate = htmlspecialchars($edu['start_date'] ?? 'N/A');
                            $endDate = htmlspecialchars($edu['end_date'] ?? 'Present');
                            $description = nl2br(htmlspecialchars($edu['description'] ?? 'N/A'));
                            echo "
                        <li style='cursor: pointer;' data-bs-toggle='modal' data-bs-target='#educationModal$eduId'>
                            $degree - $institution ($startDate - $endDate)
                        </li>
                        
                        <!-- Modal for Education -->
                        <div class='modal fade' id='educationModal$eduId' tabindex='-1' aria-labelledby='educationModalLabel$eduId' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='educationModalLabel$eduId'>$degree at $institution</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <p><strong>Field of Study:</strong> $fieldOfStudy</p>
                                        <p><strong>Duration:</strong> $startDate - $endDate</p>
                                        <p><strong>Description:</strong></p>
                                        <p>$description</p>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        }
                    } else {
                        echo "<p>No education details added.</p>";
                    }
                    $educationStmt->close();
                    ?>
                </ul>
            </div>
        </div>

        <!-- Repeat similar structure for Experience -->
        <div class="card mb-4">
            <div class="card-header">Experience</div>
            <div class="card-body">
                <ul>
                    <?php
                    $experienceStmt = $conn->prepare("SELECT * FROM experience WHERE user_id = ?");
                    $experienceStmt->bind_param("i", $seekerId);
                    $experienceStmt->execute();
                    $experienceResult = $experienceStmt->get_result();
                    if ($experienceResult->num_rows > 0) {
                        while ($exp = $experienceResult->fetch_assoc()) {
                            $expId = htmlspecialchars($exp['id']);
                            $jobTitle = htmlspecialchars($exp['job_title']);
                            $position = htmlspecialchars($exp['position']);
                            $companyName = htmlspecialchars($exp['company_name']);
                            $location = htmlspecialchars($exp['location']);
                            $startDate = htmlspecialchars($exp['start_date']);
                            $endDate = htmlspecialchars($exp['end_date'] ?? 'Present');
                            $description = nl2br(htmlspecialchars($exp['description'] ?? 'N/A'));
                            echo "
                        <li style='cursor: pointer;' data-bs-toggle='modal' data-bs-target='#experienceModal$expId'>
                            $jobTitle at $companyName ($startDate - $endDate)
                        </li>
                        
                        <!-- Modal for Experience -->
                        <div class='modal fade' id='experienceModal$expId' tabindex='-1' aria-labelledby='experienceModalLabel$expId' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='experienceModalLabel$expId'>$jobTitle at $companyName</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <p><strong>Location:</strong> $location</p>
                                        <p><strong>Position:</strong> $position</p>
                                        <p><strong>Duration:</strong> $startDate - $endDate</p>
                                        <p><strong>Description:</strong></p>
                                        <p>$description</p>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        }
                    } else {
                        echo "<p>No work experience added.</p>";
                    }
                    $experienceStmt->close();
                    ?>
                </ul>
            </div>
        </div>

        <!-- Repeat similar structure for Certifications -->
        <div class="card mb-4">
            <div class="card-header">Certifications</div>
            <div class="card-body">
                <ul>
                    <?php
                    $certStmt = $conn->prepare("SELECT * FROM certifications WHERE user_id = ?");
                    $certStmt->bind_param("i", $seekerId);
                    $certStmt->execute();
                    $certResult = $certStmt->get_result();
                    if ($certResult->num_rows > 0) {
                        while ($cert = $certResult->fetch_assoc()) {
                            $certId = htmlspecialchars($cert['id']);
                            $name = htmlspecialchars($cert['name']);
                            $organization = htmlspecialchars($cert['issuing_organization']);
                            $issueDate = htmlspecialchars($cert['issue_date']);
                            $credentialId = htmlspecialchars($cert['credential_id'] ?? 'N/A');
                            $credentialUrl = htmlspecialchars($cert['credential_url'] ?? 'N/A');
                            echo "
                        <li style='cursor: pointer;' data-bs-toggle='modal' data-bs-target='#certificationModal$certId'>
                            $name - $organization ($issueDate)
                        </li>
                        
                        <!-- Modal for Certification -->
                        <div class='modal fade' id='certificationModal$certId' tabindex='-1' aria-labelledby='certificationModalLabel$certId' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='certificationModalLabel$certId'>$name</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <p><strong>Issued By:</strong> $organization</p>
                                        <p><strong>Issue Date:</strong> $issueDate</p>
                                        <p><strong>Credential ID:</strong> $credentialId</p>
                                        <p><strong>Credential URL:</strong> <a href='$credentialUrl' target='_blank'>$credentialUrl</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        }
                    } else {
                        echo "<p>No certifications added.</p>";
                    }
                    $certStmt->close();
                    ?>
                </ul>
            </div>
        </div>

    </div>
</main>

<?php
include 'footer.php';
include 'common-footer.php';
?>