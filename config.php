<?php
// Database configuration for XAMPP/local
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "vanguard_db";

// Create connection
$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Enable error reporting for development (remove in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>

