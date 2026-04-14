<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0; // Safe access, fetch if needed

// Fallback: If user_id is not in session, fetch it once
if ($user_id === 0) {
    $stmt_uid = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    if ($stmt_uid) {
        mysqli_stmt_bind_param($stmt_uid, "s", $username);
        mysqli_stmt_execute($stmt_uid);
        $res_uid = mysqli_stmt_get_result($stmt_uid);
        if ($u = mysqli_fetch_assoc($res_uid)) {
            $user_id = $u['id'];
            $_SESSION['user_id'] = $user_id;
        }
        mysqli_stmt_close($stmt_uid);
    }
}

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

$total_bookings = 0;
$active_bookings = 0;
$total_spent = 0;

$stats_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total_bookings, SUM(CASE WHEN status IN ('confirmed','paid') AND end_date >= CURDATE() THEN 1 ELSE 0 END) AS active_bookings, SUM(CASE WHEN status IN ('confirmed','paid') THEN total_price ELSE 0 END) AS total_spent FROM bookings WHERE user_id = ?");
if ($stats_stmt) {
    mysqli_stmt_bind_param($stats_stmt, "i", $user_id);
    mysqli_stmt_execute($stats_stmt);
    $stats_result = mysqli_stmt_get_result($stats_stmt);
    $stats_row = mysqli_fetch_assoc($stats_result);
    $total_bookings = (int)($stats_row['total_bookings'] ?? 0);
    $active_bookings = (int)($stats_row['active_bookings'] ?? 0);
    $total_spent = (float)($stats_row['total_spent'] ?? 0);
    mysqli_stmt_close($stats_stmt);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-wrap {
            display: grid;
            gap: 1.2rem;
        }

        .dashboard-hero {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.12);
            padding: 2.2rem;
            background:
                linear-gradient(120deg, rgba(12,16,26,0.92), rgba(20,31,52,0.86)),
                url('https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            box-shadow: 0 20px 45px rgba(0,0,0,0.45);
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.12), rgba(0,0,0,0.42));
            pointer-events: none;
        }

        .dashboard-hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
        }

        .dashboard-kicker {
            display: inline-block;
            color: #f7dd9f;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 999px;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.85rem;
            letter-spacing: 0.45px;
        }

        .dashboard-hero h1 {
            margin: 0 0 0.6rem;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.15;
            color: #fff;
        }

        .dashboard-hero p {
            color: #d5deef;
            margin-bottom: 1.1rem;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 0.9rem;
        }

        .dashboard-stat {
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            background: linear-gradient(165deg, rgba(27,29,36,0.94), rgba(18,20,27,0.9));
            padding: 1rem;
        }

        .dashboard-stat .label {
            color: #aeb7cb;
            font-size: 0.82rem;
            margin-bottom: 0.35rem;
            display: block;
        }

        .dashboard-stat .value {
            font-size: 1.35rem;
            color: #ffe19e;
            font-weight: 800;
        }

        .dashboard-section {
            background: linear-gradient(165deg, #1b1d24, #171920);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 1.35rem;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.9rem;
            flex-wrap: wrap;
        }

        .section-head h2 {
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chip-link {
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 999px;
            color: #e3e9f6;
            text-decoration: none;
            padding: 0.36rem 0.72rem;
            font-size: 0.82rem;
            transition: all 0.25s;
            background: rgba(255,255,255,0.04);
        }

        .chip-link:hover {
            border-color: rgba(215,174,92,0.4);
            background: rgba(215,174,92,0.16);
            color: #fff3cc;
        }

        .small-list {
            display: grid;
            gap: 0.8rem;
        }

        .small-card {
            border: 1px solid rgba(255,255,255,0.09);
            background: rgba(255,255,255,0.03);
            border-radius: 14px;
            padding: 0.95rem;
        }

        .small-card h3 {
            margin: 0;
            font-size: 1rem;
        }

        .notification-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
            padding: 0.9rem 1rem;
            border-radius: 12px;
            background: rgba(215,174,92,0.14);
            border: 1px solid rgba(215,174,92,0.3);
            color: #f5e6c2;
        }

        .notification-strip strong {
            color: #fff1cb;
        }

        @media (max-width: 900px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            .dashboard-hero {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
<div class="navbar">
    <div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE</div>
    <div class="nav-links">
        <a href="dashboard.php" class="active">Home</a>
        <a href="cars.php">Browse Cars</a>
        <a href="mybookings.php">My Bookings</a>
        <a href="about.php">About Us</a>
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
    <div class="dashboard-wrap">
        <section class="dashboard-hero" data-aos="zoom-in">
            <div class="dashboard-hero-content">
                <span class="dashboard-kicker">ELITE MEMBER DASHBOARD</span>
                <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Your next drive is one click away. Manage bookings, explore the fleet, and stay updated in one place.</p>
                <div class="hero-actions">
                    <a href="cars.php" class="btn btn-primary">Browse Cars</a>
                    <a href="mybookings.php" class="btn btn-secondary">My Bookings</a>
                </div>
            </div>
        </section>

        <section class="dashboard-stats" data-aos="fade-up">
            <div class="dashboard-stat">
                <span class="label">Total Bookings</span>
                <span class="value"><?php echo $total_bookings; ?></span>
            </div>
            <div class="dashboard-stat">
                <span class="label">Active Rentals</span>
                <span class="value"><?php echo $active_bookings; ?></span>
            </div>
            <div class="dashboard-stat">
                <span class="label">Total Spent</span>
                <span class="value">₱<?php echo number_format($total_spent); ?></span>
            </div>
        </section>

        <section class="dashboard-section" data-aos="fade-right">
            <div class="section-head">
                <h2><i class="fa-solid fa-calendar-check"></i> Your Upcoming Bookings</h2>
                <a href="mybookings.php" class="chip-link">View All</a>
            </div>

            <?php
            $upcoming_bookings_query = "SELECT * FROM bookings WHERE user_id = ? AND status = 'confirmed' AND end_date >= CURDATE() ORDER BY start_date ASC LIMIT 3";
            $stmt = mysqli_prepare($conn, $upcoming_bookings_query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $upcoming_bookings_result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($upcoming_bookings_result) > 0):
            ?>
            <div class="bookings-list small-list">
                <?php while ($booking = mysqli_fetch_assoc($upcoming_bookings_result)): ?>
                <div class="booking-card small-card">
                    <div class="booking-header">
                        <h3><?php echo htmlspecialchars($booking['car_name']); ?></h3>
                        <span class="status-confirmed">CONFIRMED</span>
                    </div>
                    <div class="booking-details">
                        <div class="detail-item"><strong>Pick-up:</strong> <?php echo date('M d, Y', strtotime($booking['start_date'])); ?></div>
                        <div class="detail-item"><strong>Return:</strong> <?php echo date('M d, Y', strtotime($booking['end_date'])); ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <p>You have no upcoming bookings. <a href="cars.php">Browse cars to book one!</a></p>
            <?php endif; ?>
        </section>

        <section class="dashboard-section" data-aos="fade-left">
            <div class="section-head">
                <h2><i class="fa-solid fa-bell"></i> Latest Notifications</h2>
                <a href="notifications.php" class="chip-link">Open Notifications</a>
            </div>
            <div class="notification-strip">
                <span>You have <strong><?php echo $unread_count; ?></strong> unread notifications.</span>
                <a href="notifications.php" class="btn btn-secondary btn-small">Review Now</a>
            </div>
        </section>
    </div>
</div>

<script src="script.js?v=20260410"></script>

<div class="footer">
    <p>&copy; 2026 Elite Drive Rentals | Premium car rentals in the Philippines</p>
</div>
</body>
</html>
