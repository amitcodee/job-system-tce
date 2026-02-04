<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Check if job_id is provided
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit();
}

$jobId = intval($_GET['job_id']);
$userId = $_SESSION['user_id'];

// Fetch the user's role
$roleStmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$roleStmt->bind_param("i", $userId);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
if ($roleResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}
$userRole = $roleResult->fetch_assoc()['role'];
$roleStmt->close();

// Fetch job details
$jobStmt = $conn->prepare(
    "SELECT id, title, company_name, location, salary, description, created_at, posted_by 
     FROM jobs 
     WHERE id = ?"
);
$jobStmt->bind_param("i", $jobId);
$jobStmt->execute();
$jobResult = $jobStmt->get_result();

if ($jobResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Job not found']);
    $jobStmt->close();
    $conn->close();
    exit();
}

$job = $jobResult->fetch_assoc();

// For admin, verify they posted this job (optional security check)
if ($userRole === 'admin' && $job['posted_by'] != $userId) {
    // Allow admin to view all jobs or restrict as needed
    // echo json_encode(['success' => false, 'message' => 'Access denied']);
    // exit();
}

// Format the job data
$rawDescription = htmlspecialchars_decode($job['description'] ?? '', ENT_QUOTES);
$cleanDescription = preg_replace('/\sdata-[a-z0-9_-]+=("[^"]*"|\'[^\']*\')/i', '', $rawDescription);
$cleanDescription = strip_tags($cleanDescription, '<p><br><ul><ol><li><strong><em><b><i><h1><h2><h3><h4><h5><h6><span>');

$jobData = [
    'success' => true,
    'job' => [
        'title' => htmlspecialchars($job['title'] ?? ''),
        'company' => htmlspecialchars($job['company_name'] ?? 'N/A'),
        'location' => htmlspecialchars($job['location'] ?? 'N/A'),
        'salary' => htmlspecialchars($job['salary'] ?? 'N/A'),
        'description' => !empty($cleanDescription) ? $cleanDescription : '<p>No description available.</p>',
        'requirements' => nl2br(htmlspecialchars('No requirements listed')),
        'created_at' => htmlspecialchars($job['created_at'] ?? '')
    ]
];

echo json_encode($jobData);

$jobStmt->close();
$conn->close();
?>
