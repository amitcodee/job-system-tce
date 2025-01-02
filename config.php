<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job-system-tce";

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
?>
