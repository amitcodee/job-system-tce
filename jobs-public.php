<?php
session_start();
include 'config.php';

// Fetch all jobs, ordered by newest first
mysqli_report(MYSQLI_REPORT_OFF);
$query = "SELECT * FROM jobs ORDER BY id DESC";
$result = $conn->query($query);

$allJobs = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $allJobs[] = $row;
    }
}

// Logic: Recent is the first 5, All is the full list
$recentJobs = array_slice($allJobs, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerFlow | Premium Job Board</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f4f7ff; 
            color: #1a202c;
        }

        /* Hero Section */
        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 80px 0;
            color: white;
            border-radius: 0 0 50px 50px;
            margin-bottom: 50px;
        }

        /* Recent Card Style (Horizontal) */
        .recent-job-card {
            background: white;
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            border-left: 5px solid #6366f1;
        }
        .recent-job-card:hover {
            transform: translateX(10px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.1);
        }

        /* All Jobs Grid Style */
        .all-job-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 20px;
            transition: 0.3s;
        }
        .all-job-card:hover {
            background: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .company-logo {
            width: 50px; height: 50px;
            background: #eef2ff;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: #6366f1;
        }

        .badge-new {
            background: #ef4444; color: white;
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
        }

        .section-tag {
            color: #6366f1; font-weight: 700; text-transform: uppercase; font-size: 0.8rem;
        }
    </style>
</head>
<body>

<section class="hero-gradient text-center">
    <div class="container" data-aos="zoom-in">
        <h1 class="display-4 fw-800">Your Next Chapter Starts Here</h1>
        <p class="lead opacity-75">Explore the latest opportunities from top companies worldwide.</p>
    </div>
</section>

<main class="container">
    
    <div class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-800 mb-0 me-3">✨ Recent Postings</h3>
            <span class="badge bg-primary-subtle text-primary rounded-pill">Last 5 Uploads</span>
        </div>
        
        <div class="row g-3">
            <?php foreach ($recentJobs as $job): ?>
                <div class="col-12" data-aos="fade-up">
                    <div class="card recent-job-card p-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#jobModal<?= $job['id']; ?>" style="cursor: pointer;">
                        <div class="card-body d-flex align-items-center flex-wrap">
                            <div class="company-logo me-4"><?= strtoupper(substr($job['company_name'], 0, 1)); ?></div>
                            <div class="flex-grow-1">
                                <span class="badge badge-new rounded-pill mb-1">New</span>
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($job['title']); ?></h5>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($job['company_name']); ?> • <?= $job['location']; ?></p>
                            </div>
                            <div class="text-end ms-auto">
                                <div class="fw-800 text-primary mb-1"><?= $job['salary']; ?></div>
                                <span class="badge bg-light text-dark border"><?= $job['type']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <hr class="my-5 opacity-10">

    <div class="mb-5">
        <h3 class="fw-800 mb-4">📂 Explore All Jobs</h3>
        <div class="row g-4">
            <?php foreach ($allJobs as $job): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="card all-job-card h-100 p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="company-logo"><?= strtoupper(substr($job['company_name'], 0, 1)); ?></div>
                            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i> <?= date('M d', strtotime($job['created_at'])); ?></span>
                        </div>
                        <h5 class="fw-bold"><?= htmlspecialchars($job['title']); ?></h5>
                        <p class="text-primary fw-600 small mb-3"><?= htmlspecialchars($job['company_name']); ?></p>
                        
                        <div class="mb-4">
                            <div class="small mb-1"><i class="bi bi-geo-alt me-2"></i><?= $job['location']; ?></div>
                            <div class="small"><i class="bi bi-briefcase me-2"></i><?= $job['category']; ?></div>
                        </div>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?= $job['salary']; ?></span>
                            <button class="btn btn-outline-dark btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#jobModal<?= $job['id']; ?>">Details</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php foreach ($allJobs as $job): ?>
<div class="modal fade" id="jobModal<?= $job['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
            <div class="modal-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="company-logo me-3" style="width: 70px; height: 70px; font-size: 1.5rem;">
                        <?= strtoupper(substr($job['company_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h2 class="fw-800 mb-0"><?= $job['title']; ?></h2>
                        <p class="text-primary mb-0"><?= $job['company_name']; ?></p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4"><div class="p-3 bg-light rounded-4 text-center"><b>Location</b><br><?= $job['location']; ?></div></div>
                    <div class="col-sm-4"><div class="p-3 bg-light rounded-4 text-center"><b>Type</b><br><?= $job['type']; ?></div></div>
                    <div class="col-sm-4"><div class="p-3 bg-light rounded-4 text-center"><b>Salary</b><br><?= $job['salary']; ?></div></div>
                </div>

                <h6 class="fw-bold mb-3">Full Job Description:</h6>
                <div class="description-area p-3 border rounded-4 bg-white mb-4" style="max-height: 300px; overflow-y: auto;">
                    <?= $job['description']; ?>
                </div>

                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary btn-lg rounded-pill fw-bold">Login to Apply Now</a>
                    <?php if(!empty($job['company_website'])): ?>
                        <a href="<?= $job['company_website']; ?>" target="_blank" class="btn btn-link text-muted">Visit Company Website <i class="bi bi-box-arrow-up-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
</body>
</html>