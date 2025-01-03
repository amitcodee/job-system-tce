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

// Fetch user details
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<main id="main" class="main">
    <div class="container">
        <h2>Job Seeker Profile</h2>

        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">Personal Information</div>
            <div class="card-body">
                <form id="personalInfoForm" action="update-personal-info.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label"><strong>Full Name:</strong></label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label"><strong>Date of Birth:</strong></label>
                                <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="father_name" class="form-label"><strong>Father's Name:</strong></label>
                                <input type="text" id="father_name" name="father_name" class="form-control" value="<?= htmlspecialchars($user['father_name'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="mother_name" class="form-label"><strong>Mother's Name:</strong></label>
                                <input type="text" id="mother_name" name="mother_name" class="form-control" value="<?= htmlspecialchars($user['mother_name'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label"><strong>Address:</strong></label>
                                <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($user['address']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><strong>Email:</strong></label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label"><strong>Phone:</strong></label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="languages_known" class="form-label"><strong>Languages Known:</strong></label>
                                <input type="text" id="languages_known" name="languages_known" class="form-control" value="<?= htmlspecialchars($user['languages_known'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="profile_summary" class="form-label"><strong>Profile Summary:</strong></label>
                        <textarea id="profile_summary" name="profile_summary" class="form-control" rows="4"><?= htmlspecialchars($user['profile_summary'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resume" class="form-label"><strong>Upload Resume:</strong></label>
                        <input type="file" id="resume" name="resume" class="form-control">
                        <?php if (!empty($user['resume'])): ?>
                            <p class="mt-2">Current Resume: <a href="uploads/resumes/<?= htmlspecialchars($user['resume']); ?>" target="_blank">View Resume</a></p>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

            </div>
        </div>


        <!-- Skills -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Skills</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal">Add Skill</button>
            </div>
            <div class="card-body">
                <ul>
                    <?php
                    $skillsStmt = $conn->prepare("SELECT * FROM skills WHERE user_id = ?");
                    $skillsStmt->bind_param("i", $userId);
                    $skillsStmt->execute();
                    $skillsResult = $skillsStmt->get_result();
                    while ($skill = $skillsResult->fetch_assoc()): ?>
                        <li style="cursor: pointer; list-style-type: disc; margin-left: 20px;">
                            <span
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Click For More Details">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#skillModal<?= $skill['id']; ?>" class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($skill['skill_name']); ?>
                                    (<?= htmlspecialchars($skill['proficiency_level']); ?>)
                                </a>
                            </span>
                        </li>


                        <!-- Skill Modal -->
                        <div class="modal fade" id="skillModal<?= $skill['id']; ?>" tabindex="-1" aria-labelledby="skillModalLabel<?= $skill['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="skillModalLabel<?= $skill['id']; ?>">Skill Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Skill Name:</strong> <?= htmlspecialchars($skill['skill_name']); ?></p>
                                        <p><strong>Proficiency Level:</strong> <?= htmlspecialchars($skill['proficiency_level']); ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="delete-skill.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $skill['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                            <button type="submit" class="btn btn-danger">Delete Skill</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </ul>

            </div>
        </div>

        <!-- Education -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Education</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEducationModal">Add Education</button>
            </div>
            <div class="card-body">
                <ul>
                    <?php
                    $educationStmt = $conn->prepare("SELECT * FROM education WHERE user_id = ?");
                    $educationStmt->bind_param("i", $userId);
                    $educationStmt->execute();
                    $educationResult = $educationStmt->get_result();
                    while ($edu = $educationResult->fetch_assoc()): ?>

                        <li style="cursor: pointer; list-style-type: disc; margin-left: 20px;">
                            <span
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Click For More Details">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#educationModal<?= $edu['id']; ?>" class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($edu['degree']) . " - " . htmlspecialchars($edu['institution']); ?>
                                </a>
                            </span>
                        </li>


                        <!-- Education Modal -->
                        <div class="modal fade" id="educationModal<?= $edu['id']; ?>" tabindex="-1" aria-labelledby="educationModalLabel<?= $edu['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="educationModalLabel<?= $edu['id']; ?>">Education Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Degree:</strong> <?= htmlspecialchars($edu['degree']); ?></p>
                                        <p><strong>Institution:</strong> <?= htmlspecialchars($edu['institution']); ?></p>
                                        <p><strong>Field of Study:</strong> <?= htmlspecialchars($edu['field_of_study']); ?></p>
                                        <p><strong>Start Date:</strong> <?= htmlspecialchars($edu['start_date']); ?></p>
                                        <p><strong>End Date:</strong> <?= htmlspecialchars($edu['end_date'] ?? 'N/A'); ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($edu['description']); ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="delete-education.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $edu['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Repeat similar structure for Experience -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Experience</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExperienceModal">Add Experience</button>
            </div>
            <div class="card-body">
                <ul>
                    <?php
                    $experienceStmt = $conn->prepare("SELECT * FROM experience WHERE user_id = ?");
                    $experienceStmt->bind_param("i", $userId);
                    $experienceStmt->execute();
                    $experienceResult = $experienceStmt->get_result();
                    while ($exp = $experienceResult->fetch_assoc()): ?>
                        <li style="cursor: pointer; list-style-type: disc; margin-left: 20px;">
                            <span
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Click For More Details">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#experienceModal<?= $exp['id']; ?>" class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($exp['job_title']) . " at " . htmlspecialchars($exp['company_name']); ?>
                                </a>
                            </span>
                        </li>


                        <!-- Experience Modal -->
                        <div class="modal fade" id="experienceModal<?= $exp['id']; ?>" tabindex="-1" aria-labelledby="experienceModalLabel<?= $exp['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="experienceModalLabel<?= $exp['id']; ?>">Experience Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Job Title:</strong> <?= htmlspecialchars($exp['job_title']); ?></p>
                                        <p><strong>Position:</strong> <?= htmlspecialchars($exp['position']); ?></p>
                                        <p><strong>Company Name:</strong> <?= htmlspecialchars($exp['company_name']); ?></p>
                                        <p><strong>Location:</strong> <?= htmlspecialchars($exp['location']); ?></p>
                                        <p><strong>Start Date:</strong> <?= htmlspecialchars($exp['start_date']); ?></p>
                                        <p><strong>End Date:</strong> <?= htmlspecialchars($exp['end_date'] ?? 'N/A'); ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($exp['description']); ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="delete-experience.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $exp['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Repeat similar structure for Certifications -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Certifications</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCertificateModal">Add Certification</button>
            </div>
            <div class="card-body">
                <ul>
                    <?php
                    $certStmt = $conn->prepare("SELECT * FROM certifications WHERE user_id = ?");
                    $certStmt->bind_param("i", $userId);
                    $certStmt->execute();
                    $certResult = $certStmt->get_result();
                    while ($cert = $certResult->fetch_assoc()): ?>
                        <li style="cursor: pointer; list-style-type: disc; margin-left: 20px;">
                            <span
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Click For More Details">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#certificationModal<?= $cert['id']; ?>" class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($cert['name']); ?>
                                </a>
                            </span>
                        </li>


                        <!-- Certification Modal -->
                        <div class="modal fade" id="certificationModal<?= $cert['id']; ?>" tabindex="-1" aria-labelledby="certificationModalLabel<?= $cert['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="certificationModalLabel<?= $cert['id']; ?>">Certification Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Certificate Name:</strong> <?= htmlspecialchars($cert['name']); ?></p>
                                        <p><strong>Issuing Organization:</strong> <?= htmlspecialchars($cert['issuing_organization']); ?></p>
                                        <p><strong>Issue Date:</strong> <?= htmlspecialchars($cert['issue_date']); ?></p>
                                        <p><strong>Credential ID:</strong> <?= htmlspecialchars($cert['credential_id'] ?? 'N/A'); ?></p>
                                        <p><strong>Credential URL:</strong> <a href="<?= htmlspecialchars($cert['credential_url']); ?>" target="_blank"><?= htmlspecialchars($cert['credential_url']); ?></a></p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="delete-certification.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $cert['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

    </div>
</main>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSkillModalLabel">Add Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add-skill.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="skill_name">Skill Name *</label>
                        <input type="text" id="skill_name" name="skill_name" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="proficiency_level">Proficiency Level *</label>
                        <select id="proficiency_level" name="proficiency_level" class="form-select" required>
                            <option value="">Select Level</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Education Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEducationModalLabel">Add Education</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add-education.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="degree">Degree *</label>
                        <input type="text" id="degree" name="degree" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="institution">Institution *</label>
                        <input type="text" id="institution" name="institution" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="field_of_study">Field of Study *</label>
                        <input type="text" id="field_of_study" name="field_of_study" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Describe your education experience and achievements" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Education</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="addExperienceModal" tabindex="-1" aria-labelledby="addExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExperienceModalLabel">Add Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add-experience.php" method="POST">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="job_title">Job Title *</label>
                        <input type="text" id="job_title" name="job_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="position">Position *</label>
                        <input type="text" id="position" name="position" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="company_name">Company Name *</label>
                        <input type="text" id="company_name" name="company_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="currently_working" name="currently_working" class="form-check-input">
                        <label for="currently_working" class="form-check-label">I am currently working here</label>
                    </div>
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Experience</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="addCertificateModal" tabindex="-1" aria-labelledby="addCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCertificateModalLabel">Add Certification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add-certificate.php" method="POST">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="certificate_name">Certificate Name *</label>
                        <input type="text" id="certificate_name" name="certificate_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="issuing_organization">Issuing Organization *</label>
                        <input type="text" id="issuing_organization" name="issuing_organization" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="issue_date">Issue Date *</label>
                        <input type="date" id="issue_date" name="issue_date" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="credential_id">Credential ID</label>
                        <input type="text" id="credential_id" name="credential_id" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="credential_url">Credential URL</label>
                        <input type="url" id="credential_url" name="credential_url" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Certification</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
include 'footer.php';
include 'common-footer.php';
?>