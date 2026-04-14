<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id']; // User ID is already in session

// Handle cancellation and payment settlement
if (isset($_POST['cancel']) && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Get booking details
    $booking_query = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $booking_query);
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    mysqli_stmt_execute($stmt);
    $booking_result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($booking_result);
    
    if ($booking) {
        // Update booking status to cancelled
        $cancel_query = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $cancel_query);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
        
        // Return car to inventory
        $update_car = "UPDATE cars SET quantity = quantity + 1 WHERE name = ?";
        $stmt = mysqli_prepare($conn, $update_car);
        mysqli_stmt_bind_param($stmt, "s", $booking['car_name']);
        mysqli_stmt_execute($stmt);
        
        // Add notification for the user
        $notification_message = "Your booking for " . htmlspecialchars($booking['car_name']) . " has been successfully cancelled.";
        $notification_query = "INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'info')";
        $stmt_notif = mysqli_prepare($conn, $notification_query);
        mysqli_stmt_bind_param($stmt_notif, "is", $user_id, $notification_message);
        mysqli_stmt_execute($stmt_notif);
        $message = "Booking cancelled successfully!";
    }
}

// Handle payment settlement
if (isset($_POST['settle']) && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Get booking details
    $booking_query = "SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $booking_query);
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    mysqli_stmt_execute($stmt);
    $booking_result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($booking_result);
    
    $end_time = strtotime($booking['end_date']);
    $is_past = $end_time < time();
    if ($booking && !$is_past) {  // Check not past
        // Update booking status to paid
        $settle_query = "UPDATE bookings SET status = 'paid' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $settle_query);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
        
        // Add notification for the user
        $notification_message = "Payment settled and contract signed for " . htmlspecialchars($booking['car_name']) . "! Your car is ready. Contacts: qbjbquider@tip.edu.ph, qdjcgapasin@tip.edu.ph / 09207341118, qjkmtamayo@tip.edu.ph, qpjalappay01@tip.edu.ph";
        $notification_query = "INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'success')";
        $stmt_notif = mysqli_prepare($conn, $notification_query);
        mysqli_stmt_bind_param($stmt_notif, "is", $user_id, $notification_message);
        mysqli_stmt_execute($stmt_notif);
        
        $message = "Payment and contract completed! Contact admin to get your car.";
    } else {
    $error = "Cannot settle past or non-pending booking.";
    }
}

// Get all bookings
$bookings_query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $bookings_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$bookings_result = mysqli_stmt_get_result($stmt);

// Fetch unread notification count
$unread_count = 0;
$unread_count_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = FALSE";
$stmt_unread = mysqli_prepare($conn, $unread_count_query);
if ($stmt_unread) {
    mysqli_stmt_bind_param($stmt_unread, "i", $user_id);
    mysqli_stmt_execute($stmt_unread);
    $unread_result = mysqli_stmt_get_result($stmt_unread);
    $unread_row = mysqli_fetch_assoc($unread_result);
    $unread_count = $unread_row['unread_count'];
    mysqli_stmt_close($stmt_unread);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - Car Rental</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
<div class="navbar">
<div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE</div>
    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="cars.php">Browse Cars</a>
        <a href="mybookings.php" class="active">My Bookings</a>
        <a href="notifications.php" class="notification-icon">
            <i class="fa-regular fa-bell"></i>
            <?php if ($unread_count > 0): ?>
                <span class="notification-badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="main-content">
    <h1>My Bookings</h1>
    
    <?php if (isset($message)): ?>
    <div class="success-message">✅ <?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if (mysqli_num_rows($bookings_result) > 0): ?>
    <div class="bookings-list" data-aos="fade-up">

        <?php while ($booking = mysqli_fetch_assoc($bookings_result)): 
$status_class = $booking['status'] == 'confirmed' ? 'status-confirmed' : 
                           ($booking['status'] == 'paid' ? 'status-paid' : 
                           ($booking['status'] == 'cancelled' ? 'status-cancelled' : 
                           ($booking['status'] == 'pending' ? 'status-pending' : 'status-default')));
            
            $is_past = strtotime($booking['end_date']) < time();
        ?>
        <div class="booking-card" data-aos="zoom-in">

            <div class="booking-header">
                <div>
                    <h3>🚗 <?php echo htmlspecialchars($booking['car_name']); ?></h3>
                    <span class="<?php echo $status_class; ?>">
                        <?php echo strtoupper($booking['status']); ?>
                    </span>
                </div>
                <div class="booking-price">₱<?php echo number_format($booking['total_price']); ?></div>
            </div>
            
            <div class="booking-details">
                <div class="detail-item">
                    <strong>📅 Pick-up:</strong> 
                    <?php echo date('M d, Y', strtotime($booking['start_date'])); ?>
                </div>
                <div class="detail-item">
                    <strong>📅 Return:</strong> 
                    <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                </div>
                <div class="detail-item">
                    <strong>🕒 Booked on:</strong> 
                    <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?>
                </div>
            </div>
            
            <?php if (($booking['status'] == 'pending') && !$is_past): ?>
            <div class="booking-actions">
                <form method="POST" onsubmit="return confirm('Confirm you have settled payment and signed contract?');" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button type="submit" name="settle" class="btn btn-success">SETTLE PAYMENT & SIGN CONTRACT</button>
                </form>
                <form method="POST" onsubmit="return confirm('Cancel booking?');" style="display: inline; margin-left: 10px;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button type="submit" name="cancel" class="btn btn-danger">Cancel</button>
                </form>
            </div>
            <?php elseif ($booking['status'] == 'confirmed' && !$is_past): ?>
            <div class="booking-actions">
                <form method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button type="submit" name="cancel" class="btn btn-danger">Cancel Booking</button>
                </form>
            </div>
            <?php elseif ($booking['status'] == 'paid'): ?>
            <div class="payment-contact">
                <strong>💰 Payment Confirmed!</strong><br>
                Please email the following after the payment and try to wait 24 hours:<br>
                <strong>qbjbquider@tip.edu.ph</strong><br>
                <strong>qdjcgapasin@tip.edu.ph</strong><br>
                <strong>qgcortez@tip.edu.ph</strong><br>
                <strong>qjkmtamayo@tip.edu.ph</strong><br>
                <strong>qpjalappay01@tip.edu.ph</strong>
            </div>
            <?php endif; ?>

            <?php if ($is_past && ($booking['status'] == 'confirmed' || $booking['status'] == 'paid')): ?>
            <div class="booking-completed">✅ Rental Completed</div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="no-results">
        <p>You haven't made any bookings yet.</p>
        <a href="cars.php" class="btn btn-primary">Browse Cars</a>
    </div>
    <?php endif; ?>
</div>


<div class="footer">
    <p>&copy; 2026 Elite Drive Rentals | Premium car rentals in the Philippines</p>
</div>
</body>
</html>
