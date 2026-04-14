<?php
include 'config.php';

$username = 'admin';
$plain_password = 'admin123';
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo '<h2>Admin Setup Tool</h2>';

// Upsert users table as admin
$users_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin') ON DUPLICATE KEY UPDATE password = VALUES(password), role = 'admin'";
$users_stmt = mysqli_prepare($conn, $users_sql);
mysqli_stmt_bind_param($users_stmt, "ss", $username, $hashed_password);
$users_ok = mysqli_stmt_execute($users_stmt);
mysqli_stmt_close($users_stmt);

// Upsert admins table for legacy compatibility
$admins_sql = "INSERT INTO admins (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)";
$admins_stmt = mysqli_prepare($conn, $admins_sql);
mysqli_stmt_bind_param($admins_stmt, "ss", $username, $hashed_password);
$admins_ok = mysqli_stmt_execute($admins_stmt);
mysqli_stmt_close($admins_stmt);

if ($users_ok && $admins_ok) {
    echo "<p style='color:green;'>Admin credentials reset successfully.</p>";
    echo "<p><strong>Username:</strong> admin<br><strong>Password:</strong> admin123</p>";
} else {
    echo "<p style='color:red;'>Failed to reset admin credentials.</p>";
}

echo '<p><a href="login.php">Go to Login</a> | <a href="admin_dashboard.php">Admin Dashboard</a></p>';
echo '<p style="color:red;"><strong>Delete this file after use.</strong></p>';
?>


