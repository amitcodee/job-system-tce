<?php
session_start();
include 'config.php';

// Check if the user is logged in and has admin rights
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Process the verification
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['id'])) {
    $seekerId = $input['id'];
    $updateStmt = $conn->prepare("UPDATE users SET verified = 1 WHERE id = ?");
    $updateStmt->bind_param("i", $seekerId);
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to verify the job seeker.']);
    }
    $updateStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
