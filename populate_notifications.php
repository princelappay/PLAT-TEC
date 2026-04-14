<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die('Please login first.');
}

$user_id = $_SESSION['user_id'];
echo "Adding sample notifications for user ID: $user_id<br>";

// Sample notifications
$samples = [
    "Welcome to Elite Drive! Your account is now active.",
    "New luxury cars added to fleet: Ferrari 488 & Lamborghini Huracan.",
    "Your booking for Toyota Camry (ID#1) has been confirmed.",
    "Payment reminder: Booking ID#2 due tomorrow.",
    "Maintenance notice: Jeep Wrangler temporarily unavailable.",
    "Review request: How was your recent rental experience?"
];

foreach ($samples as $msg) {
    $query = "INSERT IGNORE INTO notifications (user_id, message, type) VALUES (?, ?, 'info')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'is', $user_id, $msg);
    if (mysqli_stmt_execute($stmt)) {
        echo "Added: $msg<br>";
    }
}

$unread_query = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = FALSE";
$stmt = mysqli_prepare($conn, $unread_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
echo "<br>Unread count: " . $row['unread'] . "<br>";
echo "<a href='notifications.php'>View Notifications</a>";
?>

