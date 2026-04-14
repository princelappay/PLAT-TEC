<?php
include 'config.php';

$result = mysqli_query($conn, "SELECT id, user_id, car_name, payment_method, status, created_at FROM bookings ORDER BY id DESC LIMIT 10");
while ($row = mysqli_fetch_assoc($result)) {
    echo implode(' | ', [
        $row['id'],
        $row['user_id'],
        $row['car_name'],
        $row['payment_method'] ?? 'NULL',
        $row['status'],
        $row['created_at']
    ]) . PHP_EOL;
}
