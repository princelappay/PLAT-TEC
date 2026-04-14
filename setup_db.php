<?php
include "config.php";

echo "<h1>🛠️ Full DB Setup - Elite Drive</h1>";
echo "<p>Creates tables + populates sample data safely (IF NOT EXISTS).</p>";

// 1. Create stock_log table if missing
$create_log = "CREATE TABLE IF NOT EXISTS stock_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_name VARCHAR(100) NOT NULL,
    old_quantity INT NOT NULL,
    new_quantity INT NOT NULL,
    delta INT NOT NULL,
    admin_id INT,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_car (car_name),
    INDEX idx_date (created_at)
)";
if (mysqli_query($conn, $create_log)) {
    echo "<p style='color:green;'>✅ stock_log table ready.</p>";
} else {
    echo "<p style='color:red;'>❌ stock_log error: " . mysqli_error($conn) . "</p>";
}

// 2. Populate cars if empty
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM cars");
$count = mysqli_fetch_assoc($count_result)['total'];
if ($count == 0) {
    $samples = [
        ['Toyota Camry', 'Sedan', 2023, 2500.00, 5, 'Comfortable sedan perfect for business trips'],
        ['Honda Civic', 'Sedan', 2023, 1800.00, 4, 'Reliable and fuel-efficient sedan'],
        ['Ford Mustang', 'Sports', 2024, 4500.00, 2, 'Powerful sports car for thrill seekers'],
        ['Tesla Model 3', 'Electric', 2024, 3500.00, 3, 'Modern electric vehicle with autopilot'],
        ['Jeep Wrangler', 'SUV', 2023, 4200.00, 3, 'Rugged SUV for off-road adventures']
    ];
    $inserted = 0;
    foreach ($samples as $car) {
        $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO cars (name, type, year, price, quantity, description) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sidds", $car[0], $car[1], $car[2], $car[3], $car[4], $car[5]);
        if (mysqli_stmt_execute($stmt)) $inserted++;
        mysqli_stmt_close($stmt);
    }
    echo "<p style='color:green;'>✅ Inserted $inserted sample cars.</p>";
} else {
    echo "<p>Cars already exist ($count).</p>";
}

// 3. Add CHECK constraint to cars table (MySQL 8.0+)
$chk_sql = "ALTER TABLE cars ADD CONSTRAINT chk_quantity CHECK (quantity >= 0)";
if (mysqli_query($conn, $chk_sql)) {
    echo "<p style='color:green;'>✅ Added CHECK constraint (quantity >= 0).</p>";
} else {
    $chk_error = mysqli_error($conn);
    if (strpos($chk_error, 'Duplicate') !== false || strpos($chk_error, 'already exists') !== false) {
        echo "<p style='color:orange;'>ℹ️ CHECK constraint already exists - OK.</p>";
    } else {
        echo "<p style='color:orange;'>ℹ️ CHECK constraint notice: $chk_error (non-blocking)</p>";
    }
}

// 3. Check admin
$admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admins"))['total'];
if ($admin_count == 0) {
    mysqli_query($conn, "INSERT IGNORE INTO admins (username, password) VALUES ('admin', '$2y$10$7rLS2RpyYubnUHzN90nRKecHEIn8GbjO6puxJD/9S7zGZCP6.A9yG')");
    echo "<p style='color:green;'>✅ Admin account ready (admin/admin123).</p>";
}

echo "<hr>";
echo "<a href='db_check.php' style='padding:12px 24px; background:#10b981; color:white; text-decoration:none; border-radius:12px; font-weight:bold;'>Check DB Status</a> ";
echo "<a href='admin_stock.php' style='padding:12px 24px; background:#059669; color:white; text-decoration:none; border-radius:12px; font-weight:bold;'>Test Stock Management</a>";
?>

