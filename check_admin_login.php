<?php
include 'config.php';

$username = 'admin';

$stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

echo "USERS admin exists: " . ($user ? "YES" : "NO") . PHP_EOL;
if ($user) {
    echo "users.role: " . ($user['role'] ?? 'NULL') . PHP_EOL;
    echo "users verify admin: " . (password_verify('admin', $user['password']) ? "TRUE" : "FALSE") . PHP_EOL;
    echo "users verify admin123: " . (password_verify('admin123', $user['password']) ? "TRUE" : "FALSE") . PHP_EOL;
}

$stmt2 = mysqli_prepare($conn, "SELECT id, password FROM admins WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt2, "s", $username);
mysqli_stmt_execute($stmt2);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
mysqli_stmt_close($stmt2);

echo "ADMINS admin exists: " . ($admin ? "YES" : "NO") . PHP_EOL;
if ($admin) {
    echo "admins verify admin: " . (password_verify('admin', $admin['password']) ? "TRUE" : "FALSE") . PHP_EOL;
    echo "admins verify admin123: " . (password_verify('admin123', $admin['password']) ? "TRUE" : "FALSE") . PHP_EOL;
}
