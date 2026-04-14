<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

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
    <title>About Us - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .about-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 2.6rem;
            margin-bottom: 1.4rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background:
                linear-gradient(120deg, rgba(11, 16, 27, 0.92), rgba(27, 38, 56, 0.85)),
                url('https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.42);
        }

        .about-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.45));
            pointer-events: none;
        }

        .about-hero-content {
            position: relative;
            z-index: 1;
            max-width: 760px;
        }

        .about-kicker {
            display: inline-block;
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.13);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #f5d995;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 0.9rem;
            font-size: 0.82rem;
        }

        .about-hero h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.15;
            margin-bottom: 0.85rem;
            color: #fff;
        }

        .about-hero p {
            color: #dbe3f4;
            font-size: 1.05rem;
            max-width: 640px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        .about-panel {
            background: linear-gradient(165deg, #1b1d24, #171920);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.4rem;
            box-shadow: var(--shadow);
        }

        .about-panel h2 {
            margin-bottom: 0.7rem;
            color: var(--text);
        }

        .about-panel p {
            color: var(--text-muted);
            margin-bottom: 0.6rem;
        }

        .value-list {
            display: grid;
            gap: 0.65rem;
            margin-top: 0.8rem;
        }

        .value-item {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.02);
        }

        .value-item i {
            color: var(--primary);
            margin-top: 0.15rem;
        }

        .value-item strong {
            color: #fff;
            display: block;
            margin-bottom: 0.15rem;
        }

        .contact-card {
            background: linear-gradient(145deg, rgba(215, 174, 92, 0.14), rgba(201, 153, 63, 0.06));
            border: 1px solid rgba(215, 174, 92, 0.28);
            border-radius: 14px;
            padding: 1rem;
            margin-top: 0.8rem;
        }

        .contact-card p {
            margin: 0.35rem 0;
            color: #f5e8c6;
        }

        .team-list {
            display: grid;
            gap: 0.45rem;
            margin-top: 0.65rem;
        }

        .team-list span {
            color: #f0f3fa;
            padding: 0.45rem 0.65rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.93rem;
        }

        .about-cta {
            margin-top: 1rem;
            text-align: center;
        }

        @media (max-width: 900px) {
            .about-grid {
                grid-template-columns: 1fr;
            }

            .about-hero {
                padding: 1.6rem;
            }
        }
    </style>
</head>
<body>
<div class="navbar">
<div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE</div>
    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="cars.php">Browse Cars</a>
        <a href="mybookings.php">My Bookings</a>
        <a href="about.php" class="active">About Us</a>
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
    <section class="about-hero" data-aos="fade-up">
        <div class="about-hero-content">
            <span class="about-kicker">ABOUT ELITE DRIVE</span>
            <h1>Your Journey, Our Priority</h1>
            <p>Elite Drive Rentals delivers stylish, dependable, and affordable car rentals for students, professionals, and families across the Philippines. We combine premium vehicles with a booking experience that is simple, transparent, and fast.</p>
        </div>
    </section>

    <div class="about-grid">
        <section class="about-panel" data-aos="fade-right">
            <h2>Why Drive With Us</h2>
            <p>We focus on comfort, safety, and service quality from reservation to return.</p>
            <div class="value-list">
                <div class="value-item">
                    <i class="fa-solid fa-car-side"></i>
                    <div>
                        <strong>Premium Fleet Selection</strong>
                        Choose from practical city cars to luxury rides for special occasions.
                    </div>
                </div>
                <div class="value-item">
                    <i class="fa-solid fa-tags"></i>
                    <div>
                        <strong>Fair & Flexible Pricing</strong>
                        Budget-friendly rates designed for everyday renters and long trips.
                    </div>
                </div>
                <div class="value-item">
                    <i class="fa-solid fa-shield-heart"></i>
                    <div>
                        <strong>Safety-First Standards</strong>
                        Every vehicle goes through regular maintenance and quality checks.
                    </div>
                </div>
                <div class="value-item">
                    <i class="fa-solid fa-bolt"></i>
                    <div>
                        <strong>Fast Booking Experience</strong>
                        Reserve in minutes, track bookings, and manage updates in one place.
                    </div>
                </div>
            </div>
        </section>

        <section class="about-panel" data-aos="fade-left">
            <h2>Our Mission</h2>
            <p>To make car rental accessible, affordable, and dependable for everyone in the Philippines, because every drive should feel elite.</p>

            <div class="contact-card">
                <p><strong>Contact Team</strong></p>
                <div class="team-list">
                    <span>qbjbquider@tip.edu.ph</span>
                    <span>qdjcgapasin@tip.edu.ph</span>
                    <span>qgcortez@tip.edu.ph</span>
                    <span>qjkmtamayo@tip.edu.ph</span>
                    <span>qpjalappay01@tip.edu.ph</span>
                </div>
                <p><strong>Phone:</strong> 09207341118</p>
            </div>

            <div class="about-cta">
                <a href="cars.php" class="btn btn-primary">Browse Available Cars</a>
            </div>
        </section>
    </div>
</div>

<div class="footer">
    <p>&copy; 2026 Elite Drive Rentals | Premium car rentals in the Philippines</p>
</div>
<script src="script.js?v=20260410"></script>
</body>
</html>