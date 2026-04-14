<?php
include 'config.php';

$correct_hash = '$2y$10$7gWaC67oGaFqlj3IyRzgOerVFZsvV/uN2rUOt0WMxLs1xJCDnnFyO';

$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = 'admin'");
mysqli_stmt_bind_param($stmt, "s", $correct_hash);
if (mysqli_stmt_execute($stmt)) {
    echo "Hash updated for admin.";
} else {
    echo "Update failed.";
}
mysqli_stmt_close($stmt);

echo "\nLogin now with admin/admin123.";
?>

