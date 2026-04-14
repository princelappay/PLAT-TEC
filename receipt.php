<?php
session_start();
include "config.php";
include "car_images.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Fetch booking and car details for the receipt
$query = "SELECT b.*, c.type, c.year, c.price as daily_rate 
          FROM bookings b 
          JOIN cars c ON b.car_name = c.name 
WHERE b.id = ? AND b.user_id = ? ";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: dashboard.php");
    exit();
}

$days = ceil((strtotime($booking['end_date']) - strtotime($booking['start_date'])) / (60 * 60 * 24));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Receipt - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260301">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .receipt-box { max-width: 600px; margin: 40px auto; padding: 40px; background: var(--bg-elevated); border: 1px solid var(--border); border-radius: var(--radius-lg); }
        .receipt-header { text-align: center; border-bottom: 1px dashed var(--border); padding-bottom: 20px; margin-bottom: 20px; }
        .receipt-row { display: flex; justify-content: space-between; margin: 12px 0; color: var(--text-muted); }
        .receipt-row strong { color: var(--text); }
        .total-row { margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--primary); font-size: 1.4rem; color: var(--primary); }
        .receipt-footer { text-align: center; margin-top: 30px; }
        @media print { .navbar, .btn, .footer { display: none; } .receipt-box { border: none; box-shadow: none; margin: 0; padding: 0; color: #000; } }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-brand"><img src="logo.png" alt="Logo" class="logo"> ELITE DRIVE</div>
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="cars.php">Fleet</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="receipt-box">
            <div class="receipt-header">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: var(--success); margin-bottom: 15px;"></i>
<h1>Booking Confirmed! Next: Settle Payment & Sign Contract</h1>
<p style="color: var(--primary); font-weight: bold;"><a href="mybookings.php" style="color: var(--primary);">→ Go to My Bookings to complete</a></p>
<p><strong>Contact for payment and contract signing:</strong><br>qgcortez@tip.edu.ph | 09877654219</p>
                <p>Booking Reference: #ED-<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></p>
            </div>
            <div class="receipt-body">
                <div class="receipt-row"><span>Vehicle</span><strong><?php echo htmlspecialchars($booking['car_name']); ?></strong></div>
                <div class="receipt-row"><span>Dates</span><strong><?php echo $booking['start_date']; ?> to <?php echo $booking['end_date']; ?></strong></div>
                <div class="receipt-row"><span>Duration</span><strong><?php echo $days; ?> Day(s)</strong></div>
                <div class="receipt-row"><span>Daily Rate</span><strong>₱<?php echo number_format($booking['daily_rate']); ?></strong></div>
                <div class="receipt-row"><span>Payment Method</span><strong><?php echo strtoupper($booking['payment_method']); ?></strong></div>
                <div class="receipt-row total-row"><span>Total Amount</span><strong>₱<?php echo number_format($booking['total_price']); ?></strong></div>
            </div>
            <div class="receipt-footer">
                <p style="margin-bottom: 20px; font-size: 0.9rem;">A confirmation email and notification have been sent to your account.</p>
                <div class="action-buttons" style="justify-content: center;">
                    <button onclick="window.print()" class="btn btn-secondary"><i class="fa-solid fa-print"></i> Print Receipt</button>
                    <a href="mybookings.php" class="btn btn-primary">My Bookings</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
