<?php
include "config.php";
echo "<h1>DB Check - Cars Table</h1>";

// Check cars count
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM cars");
$count = mysqli_fetch_assoc($count_result)['total'];
echo "<p><strong>Total Cars:</strong> $count</p>";

if ($count == 0) {
    echo '<p style="color:red;">🚨 NO CARS! Run populate_cars.php first.</p>';
    echo '<a href="populate_cars.php" style="padding:10px; background:#10b981; color:white; text-decoration:none; border-radius:8px;">Populate Sample Cars</a>';
} else {
    echo '<p style="color:green;">✅ Cars loaded!</p>';
}

// Sample rows
$cars_result = mysqli_query($conn, "SELECT name, quantity FROM cars LIMIT 5");
echo "<table border='1' style='border-collapse:collapse;'><tr><th>Name</th><th>Quantity</th></tr>";
while ($car = mysqli_fetch_assoc($cars_result)) {
    echo "<tr><td>{$car['name']}</td><td>{$car['quantity']}</td></tr>";
}
echo "</table>";

// Stock log sample (safe)
$logs = 0;
$log_result = @mysqli_query($conn, "SELECT COUNT(*) as logs FROM stock_log");
if ($log_result) {
    $logs = mysqli_fetch_assoc($log_result)['logs'] ?? 0;
}
echo "<p><strong>Stock Log Entries:</strong> $logs " . ($log_result ? '' : '(Table missing - run setup_db.php)') . "</p>";

echo "<hr>";
echo "<a href='setup_db.php' style='padding:12px; background:#ef4444; color:white;'>Full DB Setup</a> | ";
echo "<a href='admin_stock.php'>Stock Management</a>";
?>


