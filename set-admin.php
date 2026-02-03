<?php
include 'config.php';

$name = 'admin';
$email = 'admin@example.com';
$phone = '9999999999';
$password = 'admin@123';
$role = 'admin';

$hash = password_hash($password, PASSWORD_BCRYPT);

// If admin exists, update password; else insert
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1");
$check->bind_param("ss", $email, $phone);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $id = $result->fetch_assoc()['id'];
    $update = $conn->prepare("UPDATE users SET name = ?, password = ?, role = 'admin' WHERE id = ?");
    $update->bind_param("ssi", $name, $hash, $id);
    $update->execute();
    $update->close();
    echo 'Admin updated.';
} else {
    $insert = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("sssss", $name, $email, $phone, $hash, $role);
    $insert->execute();
    $insert->close();
    echo 'Admin created.';
}

$check->close();
$conn->close();
?>
