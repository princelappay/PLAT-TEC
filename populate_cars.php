<?php
include "config.php";

echo "<h1>Populating Sample Cars (if empty)</h1>";

$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM cars");
$count = mysqli_fetch_assoc($count_result)['total'];

if ($count > 0) {
    echo "<p style='color:orange;'>Cars already exist ($count). <a href='db_check.php'>Check DB</a></p>";
} else {
    // Sample cars (daily rental PHP)
    $samples = [
        ['Toyota Camry', 'Sedan', 2023, 2500, 5, 'Comfortable sedan'],
        ['Honda Civic', 'Sedan', 2023, 1800, 4, 'Reliable sedan'],
        ['Ford Mustang', 'Sports', 2024, 4500, 2, 'Powerful sports car']
    ];

    $inserted = 0;
    foreach ($samples as $car) {
        $stmt = mysqli_prepare($conn, "INSERT INTO cars (name, type, year, price, quantity, description) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "siiids", $car[0], $car[1], $car[2], $car[3], $car[4], $car[5]);
        if (mysqli_stmt_execute($stmt)) $inserted++;
        mysqli_stmt_close($stmt);
    }
    echo "<p style='color:green;'>✅ Inserted $inserted sample cars!</p>";
}

echo "<a href='db_check.php' style='padding:10px; background:#059669; color:white; text-decoration:none; border-radius:8px;'>Check DB Status</a> | ";
echo "<a href='admin_stock.php'>Go to Stock</a>";
?>

