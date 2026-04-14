<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle marking as read
if (isset($_POST['mark_read'])) {
    $notif_id = $_POST['notif_id'];
    $update_query = "UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ii", $notif_id, $user_id);
    mysqli_stmt_execute($stmt);
}

// Handle marking all as read
if (isset($_POST['mark_all_read'])) {
    $update_all = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $update_all);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
}

// Fetch all notifications
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$notifications_result = mysqli_stmt_get_result($stmt);

// Count unread for the badge
$unread_count = 0;
$unread_count_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = FALSE";
$stmt_unread = mysqli_prepare($conn, $unread_count_query);
mysqli_stmt_bind_param($stmt_unread, "i", $user_id);
mysqli_stmt_execute($stmt_unread);
$unread_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_unread));
$unread_count = $unread_row['unread_count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260301">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="navbar">
    <div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE</div>
    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="cars.php">Browse Cars</a>
        <a href="mybookings.php">My Bookings</a>
        <a href="notifications.php" class="notification-icon active">
            <i class="fa-regular fa-bell"></i>
            <?php if ($unread_count > 0): ?>
                <span class="notification-badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="quick-actions">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Notifications</h1>
            <?php if ($unread_count > 0): ?>
            <form method="POST">
                <button type="submit" name="mark_all_read" class="btn btn-secondary btn-small">Mark all as read</button>
            </form>
            <?php endif; ?>
        </div>

        <?php if (mysqli_num_rows($notifications_result) > 0): ?>
            <div class="notifications-list">
                <?php while ($notif = mysqli_fetch_assoc($notifications_result)): ?>
                    <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                        <p><?php echo htmlspecialchars($notif['message']); ?></p>
                        <span class="timestamp"><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></span>
                        <?php if (!$notif['is_read']): ?>
                            <form method="POST" class="mark-read-form">
                                <input type="hidden" name="notif_id" value="<?php echo $notif['id']; ?>">
                                <button type="submit" name="mark_read" class="btn btn-primary btn-small">Mark as Read</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No notifications yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="footer">
    <p>&copy; 2026 Elite Drive Rentals | Premium car rentals in the Philippines</p>
</div>
</body>
</html>