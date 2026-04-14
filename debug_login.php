<?php
include 'config.php';

$username = 'admin';
$password = 'admin123';

$stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

echo "<pre>";
echo "User found: " . ($user ? 'YES' : 'NO') . "\n";
if ($user) {
    echo "Role: " . $user['role'] . "\n";
    echo "Hash: " . $user['password'] . "\n";
    echo "Verify: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE') . "\n";
    echo "Full users table:\n";
    $all = mysqli_query($conn, "SELECT username, role FROM users");
    while ($r = mysqli_fetch_assoc($all)) {
        echo "- " . $r['username'] . " (" . $r['role'] . ")\n";
    }
}
echo "</pre>";

mysqli_stmt_close($stmt);
?>

