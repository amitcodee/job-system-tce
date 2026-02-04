<?php
session_start();
// Database configuration
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "job-system";

$servername = "localhost";
$username = "techcff9_smartboy";
$password = "@techcaddcomputer";
$dbname = "techcff9_jobdB";
try {
    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Ensure the connection is not closed prematurely
if (!isset($conn) || $conn === null) {
    die("Database connection is unavailable.");
}


mysqli_report(MYSQLI_REPORT_OFF);
$result = $conn->query("SELECT * FROM jobs ORDER BY id DESC");

$allJobs = [];
while ($row = $result->fetch_assoc()) {
    $allJobs[] = $row;
}

// Slice for the "Recent" section (Last 5)
$recentJobs = array_slice($allJobs, 0, 5);
// Use the top 8 for the Marquee ticker
$marqueeJobs = array_slice($allJobs, 0, 8);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerFlow Pro | Modern Job Board</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1;
            --dark: #020617;
            --glass: rgba(255, 255, 255, 0.7);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* --- AUTO SCROLL MARQUEE --- */
        .marquee-top {
            background: var(--dark);
            color: white;
            padding: 10px 0;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        .marquee-track {
            display: flex;
            width: max-content;
            animation: marquee-scroll 40s linear infinite;
        }

        .marquee-track:hover {
            animation-play-state: paused;
        }

        .marquee-item {
            display: flex;
            align-items: center;
            padding: 0 40px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        @keyframes marquee-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* --- HERO --- */
        .hero {
            background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15), transparent),
                linear-gradient(rgba(2, 6, 23, 0.9), rgba(2, 6, 23, 0.95)),
                url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            color: #fff;
            padding: 100px 0;
            text-align: center;
            border-radius: 0 0 40px 40px;
        }

        /* --- ANIMATED REVEAL --- */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- MODERN CARDS --- */
        .job-card-premium {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .job-card-premium:hover {
            transform: translateY(-8px);
            background: white;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            border-color: var(--primary);
        }

        .logo-box {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
        }

        .btn-apply-glass {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 14px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-apply-glass:hover {
            background: #4f46e5;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>

<body>

    <div class="marquee-top">
        <div class="marquee-track">
            <?php for ($i = 0; $i < 2; $i++): // Duplicate for infinite loop ?>
                <?php foreach ($marqueeJobs as $mj): ?>
                    <div class="marquee-item">
                        <span class="badge bg-primary me-2">HOT</span>
                        <?= htmlspecialchars($mj['title']) ?> at <?= htmlspecialchars($mj['company_name']) ?>
                        <span class="mx-3 text-secondary">|</span>
                    </div>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>

    <section class="hero shadow-lg">
        <div class="container" data-aos="fade-up">
            <h1 class="display-4 fw-800 mb-3">Find Your Passion Project.</h1>
            <p class="lead opacity-75 mb-4">Discover the latest opportunities in Tech, Design, and Marketing.</p>
            <a href="#recent-section" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Explore Now</a>
        </div>
    </section>

    <main class=" my-5">

        <div id="recent-section" class="  mb-5">

            <style>
                @keyframes marqueeCardScroll {
                    0% {
                        transform: translateX(0);
                    }

                    100% {
                        transform: translateX(calc(-100% / 2));
                    }

                    /* Moves halfway because we duplicate the list */
                }
            </style>

            <div
                style="background: #0f172a; padding: 40px 0; overflow: hidden; width: 100%; position: relative; border-radius: 20px;">
                <div style="display: flex; width: max-content; animation: marqueeCardScroll 30s linear infinite; gap: 20px;"
                    onmouseover="this.style.animationPlayState='paused'"
                    onmouseout="this.style.animationPlayState='running'">

                    <?php
                    // We display the recentJobs twice to create the infinite loop effect
                    $marqueeData = array_merge($recentJobs, $recentJobs);
                    foreach ($marqueeData as $index => $job):
                        ?>
                        <div class="job-card-premium" data-bs-toggle="modal" data-bs-target="#job<?= $job['id'] ?>"
                            style="flex: 0 0 350px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 25px; cursor: pointer; transition: 0.3s; color: white;">

                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div
                                    style="width: 45px; height: 45px; background: #6366f1; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; margin-right: 15px; color: white; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                                    <?= strtoupper($job['company_name'][0]) ?>
                                </div>
                                <div style="overflow: hidden;">
                                    <h6
                                        style="font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($job['title']) ?>
                                    </h6>
                                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.6); margin: 0;">
                                        <?= $job['company_name'] ?>
                                    </p>
                                </div>
                            </div>

                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                <div>
                                    <div style="font-weight: 800; color: #818cf8; font-size: 1rem;"><?= $job['salary'] ?>
                                    </div>
                                    <span
                                        style="font-size: 0.7rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px;">
                                        <i class="bi bi-geo-alt"></i> <?= $job['location'] ?>
                                    </span>
                                </div>
                                <span
                                    style="background: rgba(255,255,255,0.1); padding: 4px 12px; border-radius: 50px; font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.2);">
                                    <?= $job['type'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class=" container pt-5">
            <h3 class="fw-800 mb-4">📂 All Listed Jobs</h3>
            <div class="row g-4">
                <?php foreach ($allJobs as $job): ?>
                    <div class="col-md-6 col-lg-4 reveal">
                        <div class="job-card-premium h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between mb-4">
                                <div class="logo-box"><?= strtoupper($job['company_name'][0]) ?></div>
                                <span class="text-muted small fw-600"><i class="bi bi-clock"></i>
                                    <?= date('M d', strtotime($job['created_at'])) ?></span>
                            </div>
                            <h5 class="fw-bold"><?= htmlspecialchars($job['title']) ?></h5>
                            <p class="text-primary fw-bold small mb-4"><?= $job['company_name'] ?></p>

                            <div class="small mb-4 text-muted">
                                <div><i class="bi bi-geo-alt-fill me-2"></i><?= $job['location'] ?></div>
                                <div><i class="bi bi-tag-fill me-2"></i><?= $job['category'] ?></div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="fw-800 h5 mb-0"><?= $job['salary'] ?></span>
                                <button class="btn btn-outline-dark rounded-pill px-4" data-bs-toggle="modal"
                                    data-bs-target="#job<?= $job['id'] ?>">Details</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php foreach ($allJobs as $job): ?>
        <div class="modal fade" id="job<?= $job['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 p-4 shadow-lg" style="border-radius:30px">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="logo-box me-3"><?= strtoupper($job['company_name'][0]) ?></div>
                            <div>
                                <h3 class="fw-800 mb-0"><?= $job['title'] ?></h3>
                                <p class="text-primary mb-0"><?= $job['company_name'] ?></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="row g-3 text-center mb-4">
                            <div class="col-4 p-3 bg-light rounded-4"><strong>Location</strong><br><?= $job['location'] ?>
                            </div>
                            <div class="col-4 p-3 bg-light rounded-4"><strong>Type</strong><br><?= $job['type'] ?></div>
                            <div class="col-4 p-3 bg-light rounded-4"><strong>Salary</strong><br><?= $job['salary'] ?></div>
                        </div>
                        <h6 class="fw-bold mb-3">Job Overview</h6>
                        <div class="p-3 border rounded-4 bg-white"
                            style="max-height:250px; overflow-y:auto; line-height:1.6;">
                            <?= $job['description'] ?>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <a href="login.php" class="btn btn-apply-glass w-100 btn-lg">Apply Now</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Intersection Observer for Scroll Animations
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll(".reveal").forEach(el => observer.observe(el));
    </script>

</body>

</html>