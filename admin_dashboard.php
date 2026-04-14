<?php
session_start();
include "config.php";

// Security check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_user_id = $_SESSION['user_id'] ?? 0;
$message = '';
$error = '';

// Bookings actions only
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $query = "UPDATE bookings SET status = '$status' WHERE id = $booking_id";
    if (mysqli_query($conn, $query)) {
        $message = "Status updated!";
    } else {
        $error = "Error updating status.";
    }
}

if (isset($_POST['cancel_booking_id'])) {
    $booking_id = (int)$_POST['cancel_booking_id'];
    $car_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT car_name FROM bookings WHERE id = $booking_id"))['car_name'] ?? '';
    mysqli_query($conn, "UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id");
    if ($car_name) {
        mysqli_query($conn, "UPDATE cars SET quantity = quantity + 1 WHERE name = '" . mysqli_real_escape_string($conn, $car_name) . "'");
    }
    $message = "Booking cancelled, stock restored!";
}

// Fetch bookings (no users join)
$bookings_stmt = mysqli_prepare($conn, "SELECT * FROM bookings ORDER BY created_at DESC");
mysqli_stmt_execute($bookings_stmt);
$bookings_result = mysqli_stmt_get_result($bookings_stmt);
$total_bookings = mysqli_num_rows($bookings_result);
mysqli_stmt_close($bookings_stmt);
$revenue_stmt = mysqli_prepare($conn, "SELECT SUM(total_price) as total FROM bookings WHERE status = ?");
$status_param = "confirmed";
mysqli_stmt_bind_param($revenue_stmt, "s", $status_param);
mysqli_stmt_execute($revenue_stmt);
$total_revenue = mysqli_fetch_assoc(mysqli_stmt_get_result($revenue_stmt))['total'] ?? 0;
mysqli_stmt_close($revenue_stmt);

$unread_count = 0;
$stmt_unread = mysqli_prepare($conn, "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = FALSE");
if ($stmt_unread) {
    $user_id_param = $admin_user_id;
    mysqli_stmt_bind_param($stmt_unread, "i", $user_id_param);
    mysqli_stmt_execute($stmt_unread);
    $unread_count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_unread))['unread_count'];
    mysqli_stmt_close($stmt_unread);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background:
                radial-gradient(circle at 10% 10%, rgba(215,174,92,0.18), transparent 35%),
                radial-gradient(circle at 92% 85%, rgba(61,220,151,0.12), transparent 30%),
                linear-gradient(135deg, #0a0e17 0%, #1a1f2e 100%);
        }
        .admin-dashboard { 
            background: rgba(255,255,255,0.08); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 16px; 
            padding: 2.5rem; 
            margin: 2rem auto; 
            max-width: 1400px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .dashboard-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
        }
        .dashboard-heading h1 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .heading-chip {
            background: rgba(215,174,92,0.18);
            border: 1px solid rgba(215,174,92,0.3);
            color: #f5d995;
            border-radius: 999px;
            padding: 0.4rem 0.8rem;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .admin-section { margin-bottom: 2.5rem; }
        .admin-section h2 {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: 0.9rem;
        }
        .admin-actions {
            display: flex;
            justify-content: center;
            gap: 0.8rem;
            margin: 1.8rem 0 2rem;
            flex-wrap: wrap;
        }
        .admin-table-container { 
            background: rgba(9,12,20,0.42); 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.09);
        }
        .admin-table { 
            width: 100%; 
            font-size: 0.88rem; 
            border-collapse: collapse; 
        }
        .admin-table th { 
            background: linear-gradient(135deg, rgba(215,174,92,0.25), rgba(201,153,63,0.15)); 
            color: var(--text);
            padding: 1rem 0.8rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-table td { 
            padding: 0.9rem 0.8rem; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            vertical-align: middle;
        }
        .admin-table tr:hover { background: rgba(255,255,255,0.04); }
        .status-select {
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(0,0,0,0.5);
            color: var(--text);
            border-radius: var(--radius-sm);
            font-weight: 600;
            min-width: 120px;
        }
        .action-btn { padding: 0.5rem 1.2rem; font-size: 0.85rem; margin: 0 0.25rem; border-radius: var(--radius-sm); transition: all 0.25s; font-weight: 600; }
        .quick-link {
            color: #f1f4fb;
            text-decoration: none;
            font-weight: 700;
            padding: 0.8rem 1.15rem;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.06);
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }
        .quick-link:hover {
            transform: translateY(-1px);
            background: rgba(215,174,92,0.17);
            border-color: rgba(215,174,92,0.32);
            color: #fff4d7;
        }
        .booking-id {
            color: #ffedbf;
            font-weight: 700;
        }
        .date-stack {
            line-height: 1.35;
            color: #d8deeb;
            font-size: 0.84rem;
        }
        .price-cell {
            color: #ffd56f;
            font-weight: 800;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.55rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid transparent;
            margin-bottom: 0.4rem;
        }
        .status-pill.pending {
            color: #ffd166;
            background: rgba(255,209,102,0.14);
            border-color: rgba(255,209,102,0.36);
        }
        .status-pill.confirmed {
            color: #8df3c7;
            background: rgba(61,220,151,0.14);
            border-color: rgba(61,220,151,0.36);
        }
        .status-pill.paid {
            color: #000;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            border-color: rgba(255,193,7,0.85);
        }
        .status-pill.cancelled {
            color: #ffb6b6;
            background: rgba(255,107,107,0.16);
            border-color: rgba(255,107,107,0.36);
        }
        @media (max-width: 768px) {
            .admin-table { font-size: 0.8rem; }
            .admin-table th, .admin-table td { padding: 0.6rem 0.5rem; }
            .dashboard-heading { margin-bottom: 0.9rem; }
        }
    </style>
</head>
<body>
<div class="navbar">
    <div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE <span style="font-size: 0.8rem; color: var(--text-muted);">ADMIN</span></div>
    <div class="nav-links">
        <a href="admin_dashboard.php" class="active">Management</a>
        <a href="cars.php">Fleet View</a>
        <a href="notifications.php" class="notification-icon">
            <i class="fa-regular fa-bell"></i>
            <?php if ($unread_count > 0): ?><span class="notification-badge"><?php echo $unread_count; ?></span><?php endif; ?>
        </a>
        <a href="logout.php">Logout</a>
    </div>
</div>

    <div class="main-content admin-dashboard">
        <div class="dashboard-heading">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <span class="heading-chip">CONTROL CENTER</span>
        </div>
    
    <?php if ($message): ?><div class="success-message">✅ <?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error-message">❌ <?php echo $error; ?></div><?php endif; ?>

    <div class="stats-container">
        <div class="stat-card">
            <h3><?php echo $total_bookings; ?></h3>
            <p>All Bookings</p>
        </div>
        <div class="stat-card">
            <h3>₱<?php echo number_format($total_revenue); ?></h3>
            <p>Revenue</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $unread_count; ?></h3>
            <p>Notifications</p>
        </div>
        <div class="stat-card" onclick="window.location.href='admin_stock.php'" style="cursor:pointer; transition: all 0.25s;">
            <h3><i class="fas fa-warehouse"></i></h3>
            <p>Stock Management</p>
        </div>
    </div>

    <div class="admin-actions">
        <a href="admin_stock.php" class="quick-link">
            <i class="fas fa-boxes-stacked"></i> Manage Inventory
        </a>
        <a href="notifications.php" class="quick-link">
            <i class="fas fa-bell"></i> View Notifications
        </a>
    </div>

    <div class="admin-section">
        <h2><i class="fas fa-calendar-check"></i> All Bookings (User IDs Only)</h2>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Car</th>
                        <th>Start → End</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Booked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($bookings_result)): ?>
                    <tr>
                        <td><span class="booking-id">#<?php echo $row['id']; ?></span></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['car_name']); ?></td>
                        <td class="date-stack"><?php echo date('M d', strtotime($row['start_date'])); ?> → <br><?php echo date('M d', strtotime($row['end_date'])); ?></td>
                        <td><span class="price-cell">₱<?php echo number_format($row['total_price']); ?></span></td>
                        <td>
                            <span class="status-pill <?php echo htmlspecialchars($row['status']); ?>"><?php echo strtoupper(htmlspecialchars($row['status'])); ?></span><br>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="update_status" value="1">
                                <select name="status" onchange="this.form.submit();" class="status-select">
                                    <option value="pending" <?php echo $row['status']=='pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                    <option value="confirmed" <?php echo $row['status']=='confirmed' ? 'selected' : ''; ?>>✅ Confirmed</option>
                                    <option value="cancelled" <?php echo $row['status']=='cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                                    <option value="paid" <?php echo $row['status']=='paid' ? 'selected' : ''; ?>>💰 Paid</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo date('M j, H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php if ($row['status'] !== 'cancelled'): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Cancel booking &amp; restore car stock?');">
                                <input type="hidden" name="cancel_booking_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="action-btn btn btn-danger">Cancel &amp; Restore</button>
                            </form>
                            <?php else: ?>
                            <span class="badge badge-unavailable">Cancelled</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2026 Elite Drive | Bookings Admin Panel - Functional &amp; Clean</p>
</div>
</body>
</html>