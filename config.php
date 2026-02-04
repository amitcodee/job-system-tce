<?php
// Database configuration (environment overrides supported)
// Default values are for local WAMP setups.
// $servername = getenv('DB_HOST') ?: "localhost";
// $username = getenv('DB_USER') ?: "root";
// $password = getenv('DB_PASS') ?: "";
// $dbname = getenv('DB_NAME') ?: "job-system";

$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "techcff9_smartboy";
$password = getenv('DB_PASS') ?: "@techcaddcomputer";
$dbname = getenv('DB_NAME') ?: "techcff9_jobdB";


if (!class_exists('mysqli')) {
    die('Database driver (mysqli) is not enabled on the server.');
}

// Create a new database connection
$conn = @new mysqli($servername, $username, $password, $dbname);

// Check the connection
if (!$conn || $conn->connect_error) {
    $errorMessage = $conn ? $conn->connect_error : 'Unknown connection error';
    die("Database connection error: " . $errorMessage);
}

// Ensure the connection is not closed prematurely
if (!isset($conn) || $conn === null) {
    die("Database connection is unavailable.");
}
?>
