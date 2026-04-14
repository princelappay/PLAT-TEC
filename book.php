<?php
session_start();
include "config.php";
include "car_images.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id']; // User ID is already in session
$message = '';
$error = '';

$unread_count = 0;
$stmt_unread = mysqli_prepare($conn, "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = FALSE");
if ($stmt_unread) {
    mysqli_stmt_bind_param($stmt_unread, "i", $user_id);
    mysqli_stmt_execute($stmt_unread);
    $unread_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_unread));
    $unread_count = (int)($unread_data['unread_count'] ?? 0);
    mysqli_stmt_close($stmt_unread);
}


// Get car name from URL
$car_name = isset($_GET['car']) ? $_GET['car'] : '';

// Get car details
$car_query = "SELECT * FROM cars WHERE name = ? AND quantity > 0";
$stmt = mysqli_prepare($conn, $car_query);
mysqli_stmt_bind_param($stmt, "s", $car_name);
mysqli_stmt_execute($stmt);
$car_result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($car_result);

if (!$car) {
    header("Location: cars.php");
    exit();
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'card';
    if (!in_array($payment_method, ['card', 'cash'], true)) {
        $payment_method = 'card';
    }
    
    // Validate dates
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    $today = strtotime(date('Y-m-d'));
    
    if ($start < $today) {
        $error = "Start date cannot be in the past!";
    } elseif ($end <= $start) {
        $error = "End date must be after start date!";
    } else {
        // Calculate total price
        $days = ceil(($end - $start) / (60 * 60 * 24));
        $total_price = $days * $car['price'];
        
        // Check if car is still available
        if ($car['quantity'] > 0) {
            // Create booking
$booking_query = "INSERT INTO bookings (user_id, car_name, start_date, end_date, total_price, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = mysqli_prepare($conn, $booking_query);
mysqli_stmt_bind_param($stmt, "isssds", $user_id, $car_name, $start_date, $end_date, $total_price, $payment_method);
            
            if (mysqli_stmt_execute($stmt)) {
                $booking_id = mysqli_insert_id($conn);

                // Update car quantity
                $new_quantity = $car['quantity'] - 1;
                $update_query = "UPDATE cars SET quantity = ? WHERE name = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "is", $new_quantity, $car_name);
                mysqli_stmt_execute($stmt);
                
                // Add notification for the user
$notification_message = "Your booking for " . htmlspecialchars($car_name) . " from " . $start_date . " to " . $end_date . " has been created (Pending payment)!";
$notification_query = "INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'success')";
$stmt_notif = mysqli_prepare($conn, $notification_query);
mysqli_stmt_bind_param($stmt_notif, "is", $user_id, $notification_message);
mysqli_stmt_execute($stmt_notif);

                header("Location: receipt.php?id=" . (int)$booking_id);
                exit();
            } else {
                $error = "Booking failed. Please try again.";
            }
        } else {
            $error = "Sorry, this car is no longer available.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Car - Car Rental</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
<div class="navbar">
<div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE</div>
    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="cars.php">Browse Cars</a>
        <a href="mybookings.php">My Bookings</a>
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
    <div class="booking-container">
        <h1>Book Your Car</h1>
        
        <?php if ($message): ?>
        <div class="success-message">
            ✅ <?php echo $message; ?>
            <div style="margin-top: 15px;">
                <a href="mybookings.php" class="btn btn-primary">View My Bookings</a>
                <a href="cars.php" class="btn btn-secondary">Browse More Cars</a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="error-message">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="booking-layout" data-aos="fade-up">

            <div class="car-summary">
            <img src="<?php echo htmlspecialchars(get_car_image($car['name'])); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="car-photo-xlarge" loading="lazy">

                <h2><?php echo htmlspecialchars($car['name']); ?></h2>
                <div class="summary-details">
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($car['type']); ?></p>
                    <p><strong>Year:</strong> <?php echo $car['year']; ?></p>
                    <p><strong>Daily Rate:</strong> ₱<?php echo number_format($car['price']); ?></p>
                    <p><strong>Available:</strong> <?php echo $car['quantity']; ?> units</p>
                </div>
                <p class="car-desc"><?php echo htmlspecialchars($car['description']); ?></p>
            </div>
            
            <div class="booking-form">
                <h3>Rental Details</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Pick-up Date:</label>
                        <input type="date" name="start_date" 
                               min="<?php echo date('Y-m-d'); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label>Return Date:</label>
                        <input type="date" name="end_date" 
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                               required>
                    </div>
                    
                    <h3>Payment Option</h3>
                    <div class="payment-options">
                        <input type="radio" id="payment_card" name="payment_method" value="card" checked style="position:absolute; opacity:0; width:1px; height:1px;">
                        <label class="payment-card active" for="payment_card" data-payment="card">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Credit Card</span>
                        </label>
                        <input type="radio" id="payment_cash" name="payment_method" value="cash" style="position:absolute; opacity:0; width:1px; height:1px;">
                        <label class="payment-card" for="payment_cash" data-payment="cash">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <span>Cash</span>
                        </label>
                    </div>
                    
                    <div class="price-info">
                        <p>Base rate: <strong>₱<?php echo number_format($car['price']); ?>/day</strong></p>
                        <p class="note">Total price will be calculated based on rental duration</p>
                    </div>
                    
                    <button type="submit" name="book" class="btn btn-primary btn-full">Confirm Booking</button>
                    <a href="cars.php" class="btn btn-secondary btn-full">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2026 Elite Drive Rentals | Premium car rentals in the Philippines</p>
</div>

    <script src="script.js?v=20260410"></script>
    <script>

// Calculate and display price estimate
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.addEventListener('change', function() {
        const startDate = new Date(document.querySelector('input[name="start_date"]').value);
        const endDate = new Date(document.querySelector('input[name="end_date"]').value);
        
        if (startDate && endDate && endDate > startDate) {
            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const total = days * <?php echo $car['price']; ?>;
            document.querySelector('.price-info .note').innerHTML = 
                `Rental duration: ${days} day(s) × ₱<?php echo number_format($car['price']); ?> = <strong>₱${total.toLocaleString()}</strong>`;
        }
    });
});

// Payment card active state sync
const paymentCards = document.querySelectorAll('.payment-card');
const paymentInputs = document.querySelectorAll('input[name="payment_method"]');

function syncPaymentSelection() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value || 'card';
    paymentCards.forEach(card => {
        card.classList.toggle('active', card.dataset.payment === selected);
    });
}

paymentInputs.forEach(input => {
    input.addEventListener('change', syncPaymentSelection);
});

paymentCards.forEach(card => {
    card.addEventListener('click', () => {
        const method = card.dataset.payment;
        const targetInput = document.querySelector(`input[name="payment_method"][value="${method}"]`);
        if (targetInput) {
            targetInput.checked = true;
            syncPaymentSelection();
        }
    });
});

syncPaymentSelection();
</script>
</body>
</html>
