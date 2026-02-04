<?php
session_start();
// Database configuration
$servername = "localhost";
$username = "techcff9_smartboy";
$password = "@techcaddcomputer";
$dbname = "techcff9_jobdB";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

if (!isset($conn) || $conn === null) {
    die("Database connection is unavailable.");
}

mysqli_report(MYSQLI_REPORT_OFF);
$result = $conn->query("SELECT * FROM jobs ORDER BY id DESC");

$allJobs = [];
while ($row = $result->fetch_assoc()) {
    $allJobs[] = $row;
}

$recentJobs = array_slice($allJobs, 0, 6);
$marqueeJobs = array_slice($allJobs, 0, 10);

// Get unique values for filters
$locations = array_unique(array_column($allJobs, 'location'));
$categories = array_unique(array_column($allJobs, 'category'));
$types = array_unique(array_column($allJobs, 'type'));

// Prepare stats
$uniqueCompanies = count(array_unique(array_column($allJobs, 'company_name')));
$totalJobs = count($allJobs);
$totalCategories = count($categories);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCADD Placement Cell | Your Career Gateway</title>
    <link rel="icon" type="image/x-icon" href="https://api.dicebear.com/7.x/avataaars/svg?seed=TechCADD">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --primary-light: #60A5FA;
            --secondary: #8B5CF6;
            --accent: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --dark: #0F172A;
            --dark-blue: #1E293B;
            --gray-900: #0F172A;
            --gray-800: #1E293B;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748B;
            --gray-400: #94A3B8;
            --gray-300: #CBD5E1;
            --gray-200: #E2E8F0;
            --gray-100: #F1F5F9;
            --gray-50: #F8FAFC;
            --light: #F0F9FF;
            --white: #FFFFFF;
            
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.08);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.1), 0 2px 4px -1px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.1), 0 4px 6px -2px rgba(15, 23, 42, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
            
            --gradient-primary: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            --gradient-dark: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            --gradient-success: linear-gradient(135deg, #10B981 0%, #059669 100%);
            --gradient-warning: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.03) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-primary);
            border-radius: 10px;
            border: 2px solid var(--gray-100);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Navigation */
        .navbar-premium {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }

        .navbar-premium.scrolled {
            padding: 0.8rem 0;
            box-shadow: var(--shadow-lg);
            background: rgba(255, 255, 255, 0.98);
        }

        .brand-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            position: relative;
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: transform 0.3s ease;
        }

        .brand-logo:hover .brand-icon {
            transform: rotate(10deg) scale(1.05);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: 900;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .brand-tagline {
            font-size: 0.7rem;
            color: var(--gray-500);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Hero Section */
        .hero-section {
            padding: 180px 0 100px;
            background: linear-gradient(135deg, 
                rgba(15, 23, 42, 0.98) 0%,
                rgba(30, 41, 59, 0.98) 100%),
                url('https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .hero-badge i {
            color: var(--warning);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: white;
            letter-spacing: -1px;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, #60A5FA 0%, #8B5CF6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 3rem;
            max-width: 700px;
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        /* Search Container */
        .search-container {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow-2xl);
            animation: slideUp 0.8s ease 0.8s both;
            position: relative;
            z-index: 10;
        }

        .search-container::before {
            content: '';
            position: absolute;
            inset: -1px;
            background: var(--gradient-primary);
            border-radius: 25px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .search-container:hover::before {
            opacity: 1;
        }

        .search-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: center;
        }

        .search-field {
            position: relative;
        }

        .search-field i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.25rem;
        }

        .search-input {
            width: 100%;
            padding: 1.125rem 1.25rem 1.125rem 3.5rem;
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .search-select {
            width: 100%;
            padding: 1.125rem 1.25rem;
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            font-size: 0.95rem;
            font-weight: 600;
            background: var(--gray-50);
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            padding-right: 3rem;
        }

        .search-select:focus {
            outline: none;
            border-color: var(--primary);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-search {
            padding: 1.125rem 2.5rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-search:active {
            transform: translateY(0);
        }

        .btn-search::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-search:hover::after {
            left: 100%;
        }

        /* Stats Section */
        .stats-section {
            padding: 6rem 0;
            background: var(--gray-50);
            position: relative;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
            border-color: var(--primary-light);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            position: relative;
        }

        .stat-icon-inner {
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-icon-inner {
            transform: rotate(5deg) scale(1.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Featured Jobs */
        .featured-section {
            padding: 6rem 0;
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-badge {
            display: inline-block;
            background: var(--gradient-primary);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            color: var(--gray-900);
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            color: var(--gray-600);
            font-size: 1.125rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .featured-carousel {
            position: relative;
            padding: 2rem 0;
        }

        .swiper {
            width: 100%;
            padding: 2rem;
        }

        .swiper-slide {
            height: auto;
        }

        .featured-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .featured-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
        }

        .featured-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
            border-color: var(--primary-light);
        }

        .featured-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--gradient-warning);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Job Cards */
        .job-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-light);
        }

        .job-type-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .job-company-logo {
            width: 64px;
            height: 64px;
            background: var(--gradient-primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .job-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--gray-900);
            line-height: 1.3;
        }

        .job-company {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .job-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .job-meta-item i {
            color: var(--primary);
            font-size: 1rem;
        }

        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .job-salary {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--gray-900);
        }

        .btn-view {
            padding: 0.75rem 1.5rem;
            background: var(--gray-900);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: var(--primary);
            transform: translateX(4px);
        }

        /* Modal */
        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: var(--shadow-2xl);
            overflow: hidden;
        }

        .modal-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2.5rem;
            border: none;
        }

        .modal-job-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .modal-company {
            font-size: 1.125rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 3rem;
            }
        }

        @media (max-width: 992px) {
            .search-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 150px 0 80px;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.125rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.75rem;
            }
            
            .hero-badge {
                font-size: 0.85rem;
            }
            
            .brand-name {
                font-size: 1.25rem;
            }
            
            .btn-search {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar-premium" id="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="#" class="brand-logo">
                    <div class="brand-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div class="brand-text">
                        <div class="brand-name">TechCADD</div>
                        <div class="brand-tagline">Placement Cell</div>
                    </div>
                </a>
                
                <div class="nav-actions">
                    <a href="https://techcadd.com/placement-cell/login.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Announcement Marquee -->
    <div class="bg-dark text-white py-3">
        <div class="container">
            <marquee behavior="scroll" direction="left" scrollamount="5" class="d-flex align-items-center gap-4">
                <?php foreach ($marqueeJobs as $mj): ?>
                    <span class="d-inline-flex align-items-center gap-2 me-4">
                        <span class="badge bg-danger animate__animated animate__pulse animate__infinite">🔥 HOT</span>
                        <strong><?= htmlspecialchars($mj['title']) ?></strong> at <?= htmlspecialchars($mj['company_name']) ?>
                        <span class="text-muted">•</span>
                    </span>
                <?php endforeach; ?>
            </marquee>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="hero-badge animate__animated animate__fadeIn">
                        <i class="bi bi-star-fill"></i>
                        <span class="text-white">Your Career Gateway</span>
                    </div>
                    
                    <h1 class="hero-title">
                        Build Your <span class="gradient-text">Dream Career</span> With TechCADD
                    </h1>
                    
                    <p class="hero-subtitle">
                        Discover thousands of opportunities from leading companies. Your perfect job is just a search away.
                    </p>
                    
                    <!-- Search Box -->
                    <div class="search-container">
                        <div class="search-grid">
                            <div class="search-field">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchInput" class="search-input" 
                                       placeholder="Job title, keywords, or company...">
                            </div>
                            
                            <select id="locationFilter" class="search-select">
                                <option value="">All Locations</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select id="categoryFilter" class="search-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <button class="btn-search" onclick="filterJobs()">
                                <i class="bi bi-search me-2"></i>Find Jobs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <div class="stat-icon">
                        <div class="stat-icon-inner">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?= $totalJobs ?>+</div>
                    <div class="stat-label">Active Jobs</div>
                </div>
                
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <div class="stat-icon">
                        <div class="stat-icon-inner">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?= $uniqueCompanies ?>+</div>
                    <div class="stat-label">Companies</div>
                </div>
                
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <div class="stat-icon">
                        <div class="stat-icon-inner">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?= $totalCategories ?>+</div>
                    <div class="stat-label">Categories</div>
                </div>
                
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                    <div class="stat-icon">
                        <div class="stat-icon-inner">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Candidates Placed</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Jobs -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge animate__animated animate__fadeIn">Featured Jobs</span>
                <h2 class="section-title animate__animated animate__fadeIn" style="animation-delay: 0.2s">
                    Trending Opportunities
                </h2>
                <p class="section-subtitle animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                    Handpicked premium positions from top-tier companies
                </p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($recentJobs as $job): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="featured-card">
                        <span class="featured-badge">🔥 Hot</span>
                        
                        <div class="d-flex align-items-start mb-4">
                            <div class="job-company-logo me-3">
                                <?= strtoupper($job['company_name'][0]) ?>
                            </div>
                            <div>
                                <h4 class="job-title mb-1"><?= htmlspecialchars($job['title']) ?></h4>
                                <p class="job-company mb-0"><?= htmlspecialchars($job['company_name']) ?></p>
                            </div>
                        </div>
                        
                        <div class="job-meta">
                            <div class="job-meta-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span><?= htmlspecialchars($job['location']) ?></span>
                            </div>
                            <div class="job-meta-item">
                                <i class="bi bi-clock-fill"></i>
                                <span><?= htmlspecialchars($job['type']) ?></span>
                            </div>
                        </div>
                        
                        <div class="job-footer">
                            <div class="job-salary"><?= htmlspecialchars($job['salary']) ?></div>
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#job<?= $job['id'] ?>">
                                View <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- All Jobs -->
    <section class="bg-gray-50 py-6">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">All Available Positions</h2>
                <p class="section-subtitle">Explore our complete collection of career opportunities</p>
            </div>
            
            <div class="row g-4" id="jobsContainer">
                <?php foreach ($allJobs as $job): ?>
                <div class="col-md-6 col-lg-4 job-item"
                     data-title="<?= strtolower($job['title']) ?>"
                     data-company="<?= strtolower($job['company_name']) ?>"
                     data-location="<?= strtolower($job['location']) ?>"
                     data-category="<?= strtolower($job['category']) ?>"
                     data-type="<?= strtolower($job['type']) ?>">
                    
                    <div class="job-card">
                        <span class="job-type-badge"><?= htmlspecialchars($job['type']) ?></span>
                        
                        <div class="job-company-logo mb-3">
                            <?= strtoupper($job['company_name'][0]) ?>
                        </div>
                        
                        <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
                        <div class="job-company"><?= htmlspecialchars($job['company_name']) ?></div>
                        
                        <div class="job-meta">
                            <div class="job-meta-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span><?= htmlspecialchars($job['location']) ?></span>
                            </div>
                            <div class="job-meta-item">
                                <i class="bi bi-tag-fill"></i>
                                <span><?= htmlspecialchars($job['category']) ?></span>
                            </div>
                        </div>
                        
                        <div class="job-footer">
                            <div class="job-salary"><?= htmlspecialchars($job['salary']) ?></div>
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#job<?= $job['id'] ?>">
                                View <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5" id="noResults" style="display: none;">
                <div class="py-5">
                    <i class="bi bi-search display-1 text-gray-300 mb-4"></i>
                    <h3 class="h4 text-gray-700 mb-2">No jobs found</h3>
                    <p class="text-gray-500">Try adjusting your search or filters</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modals -->
    <?php foreach ($allJobs as $job): ?>
    <div class="modal fade" id="job<?= $job['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-job-title"><?= htmlspecialchars($job['title']) ?></h2>
                        <p class="modal-company"><?= htmlspecialchars($job['company_name']) ?></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                                <i class="bi bi-geo-alt-fill fs-4 text-primary"></i>
                                <div>
                                    <div class="text-muted small">Location</div>
                                    <div class="fw-semibold"><?= htmlspecialchars($job['location']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                                <i class="bi bi-clock-fill fs-4 text-primary"></i>
                                <div>
                                    <div class="text-muted small">Job Type</div>
                                    <div class="fw-semibold"><?= htmlspecialchars($job['type']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                                <i class="bi bi-cash-stack fs-4 text-primary"></i>
                                <div>
                                    <div class="text-muted small">Salary</div>
                                    <div class="fw-semibold"><?= htmlspecialchars($job['salary']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                                <i class="bi bi-tag-fill fs-4 text-primary"></i>
                                <div>
                                    <div class="text-muted small">Category</div>
                                    <div class="fw-semibold"><?= htmlspecialchars($job['category']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3 fw-semibold">
                            <i class="bi bi-file-text-fill text-primary me-2"></i>
                            Job Description
                        </h5>
                        <div class="bg-light p-4 rounded" style="max-height: 300px; overflow-y: auto;">
                            <?= strip_tags(htmlspecialchars_decode($job['description'], ENT_QUOTES), '<p><br><strong><em><ul><ol><li><a><b><i><u><span><h1><h2><h3><h4><h5><h6>') ?>
                        </div>
                    </div>
                    
                    <a href="https://techcadd.com/placement-cell/login.php" class="btn btn-primary btn-lg w-100 py-3 fw-semibold">
                        <i class="bi bi-send-fill me-2"></i>
                        Apply for this Position
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="brand-logo">
                        <div class="brand-icon">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div class="brand-text">
                            <div class="brand-name">TechCADD</div>
                            <div class="brand-tagline" style="color: var(--gray-400);">Placement Cell</div>
                        </div>
                    </div>
                    <p class="mt-3 text-gray-400 mb-0">
                        Your gateway to successful career opportunities.
                    </p>
                </div>
                <div class="col-md-6 text-md-end mt-4 mt-md-0">
                    <div class="h4 mb-3">Ready to advance your career?</div>
                    <a href="https://techcadd.com/placement-cell/login.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
                    </a>
                </div>
            </div>
            <div class="text-center text-gray-500 mt-5 pt-4 border-top border-gray-800">
                <small>© <?= date('Y') ?> TechCADD Placement Cell. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Search and filter function
        function filterJobs() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const locationFilter = document.getElementById('locationFilter').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            
            const jobItems = document.querySelectorAll('.job-item');
            let visibleCount = 0;
            
            jobItems.forEach(item => {
                const title = item.getAttribute('data-title');
                const company = item.getAttribute('data-company');
                const location = item.getAttribute('data-location');
                const category = item.getAttribute('data-category');
                
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm) || 
                    company.includes(searchTerm);
                const matchesLocation = !locationFilter || location.includes(locationFilter);
                const matchesCategory = !categoryFilter || category.includes(categoryFilter);
                
                if (matchesSearch && matchesLocation && matchesCategory) {
                    item.style.display = 'block';
                    item.classList.add('animate__animated', 'animate__fadeIn');
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('noResults');
            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
            
            // Animate results
            document.querySelectorAll('.job-item[style*="block"]').forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });
        }

        // Event listeners for real-time filtering
        document.getElementById('searchInput').addEventListener('input', filterJobs);
        document.getElementById('locationFilter').addEventListener('change', filterJobs);
        document.getElementById('categoryFilter').addEventListener('change', filterJobs);
        
        // Enter key to search
        document.getElementById('searchInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') filterJobs();
        });

        // Initialize animations on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Animate stats
            const stats = document.querySelectorAll('.stat-card');
            stats.forEach((stat, index) => {
                setTimeout(() => {
                    stat.classList.add('animate__animated', 'animate__fadeInUp');
                }, index * 100);
            });
            
            // Initialize all animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeIn');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.featured-card, .job-card').forEach(card => {
                observer.observe(card);
            });
        });

        // Modal animation
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                const modalContent = modal.querySelector('.modal-content');
                modalContent.classList.add('animate__animated', 'animate__zoomIn');
            });
        });
    </script>
</body>
</html>