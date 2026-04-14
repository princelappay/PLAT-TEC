<?php
include 'config.php';

// Ensure admin user exists
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
echo "New admin123 hash: " . $admin_hash . "\n";

$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO users (username, password, role) VALUES ('admin', ?, 'admin')");
mysqli_stmt_bind_param($stmt, "s", $admin_hash);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO admins (username, password) VALUES ('admin', ?)");
mysqli_stmt_bind_param($stmt, "s", $admin_hash);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "Admin user 'admin'/'admin123' created/verified. Run this once.";
?>

