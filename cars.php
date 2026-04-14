<?php
session_start();
include "config.php";
include "car_images.php";

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

// Get filter parameters
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'All';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Build query
$query = "SELECT * FROM cars"; // Select all cars, regardless of quantity
if ($type_filter != 'All') {
    $query .= " WHERE type = '" . mysqli_real_escape_string($conn, $type_filter) . "'";
}

switch($sort) {
    case 'price_low':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY price DESC";
        break;
    case 'year':
        $query .= " ORDER BY year DESC";
        break;
    default:
        $query .= " ORDER BY name ASC";
}

$cars_result = mysqli_query($conn, $query);

// Get all car types for filter
$types_query = "SELECT DISTINCT type FROM cars ORDER BY type";
$types_result = mysqli_query($conn, $types_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Cars - Car Rental</title>
    <link rel="stylesheet" href="style.css?v=20260301">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
<div class="navbar">
<div class="nav-brand"><img src="logo.png" alt="Elite Drive Logo" class="logo"> ELITE DRIVE</div>
    <div class="nav-links">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a>
        <?php endif; ?>
        <a href="dashboard.php">Home</a>
        <a href="cars.php" class="active">Browse Cars</a>
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
    <h1>Available Cars for Rent</h1>
    
<div class="filters" data-aos="fade-down">
        <form method="GET" class="filter-form row g-3">

            <div class="filter-group">
                <label>Type:</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="All" <?php echo $type_filter == 'All' ? 'selected' : ''; ?>>All Types</option>
                    <?php while ($type = mysqli_fetch_assoc($types_result)): ?>
                    <option value="<?php echo $type['type']; ?>" <?php echo $type_filter == $type['type'] ? 'selected' : ''; ?>>
                        <?php echo $type['type']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Sort by:</label>
                <select name="sort" onchange="this.form.submit()">
                    <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                    <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="year" <?php echo $sort == 'year' ? 'selected' : ''; ?>>Year (Newest)</option>
                </select>
            </div>
        </form>
    </div>

    <div class="row g-4 car-grid list-grid" data-aos="fade-up">

        <?php 
        if (mysqli_num_rows($cars_result) > 0):
            while ($car = mysqli_fetch_assoc($cars_result)): 
        ?>
        <div class="car-card-large">
            <img src="<?php echo htmlspecialchars(get_car_image($car['name'])); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="car-photo-large" loading="lazy">

            <div class="car-details">
                <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                <div class="car-info">
                    <span class="badge"><?php echo htmlspecialchars($car['type']); ?></span>
                    <span class="badge">Year: <?php echo $car['year']; ?></span>
                    <?php if ($car['quantity'] > 0): ?>
                        <span class="badge-available"><?php echo $car['quantity']; ?> available</span>
                    <?php else: ?>
                        <span class="badge-unavailable">Out of Stock</span>
                    <?php endif; ?>
                </div>
                <p class="car-description"><?php echo htmlspecialchars($car['description']); ?></p>
                <div class="car-footer">
                    <div class="car-price-large">₱<?php echo number_format($car['price']); ?><span>/day</span></div>
                    <a href="book.php?car=<?php echo urlencode($car['name']); ?>" class="btn btn-primary">Book Now</a>
                </div>
                <?php if ($car['quantity'] <= 0): ?>
                    <div class="overlay-out-of-stock">Out of Stock</div>
                <?php endif; ?>
            </div>
        </div>
        <?php 
            endwhile;
        else:
        ?>
        <div class="no-results">
            <p>No cars found matching your criteria.</p>
            <a href="cars.php" class="btn btn-secondary">Clear Filters</a>
        </div>
        <?php endif; ?>
    </div>
    <script src="script.js"></script>
</div>

<div class="footer">
    <p>&copy; 2026 Elite Drive Rentals | Premium car rentals in the Philippines</p>
</div>
</body>
</html>

