<?php
session_start();
include "config.php";

$message = "";
$error = "";

// Dynamic error reporting for debug - add after includes, before security
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
if ($debug) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// Security check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle stock update
if (isset($_POST['update_stock'])) {
    $car_name = $_POST['car_name'];
    $add_stock = (int)($_POST['add_stock'] ?? 0);
    $reduce_stock = (int)($_POST['reduce_stock'] ?? 0);
    $reason = 'Stock adjustment';
    $delta = $add_stock > 0 ? $add_stock : -$reduce_stock;

    // Verify car exists with prepared stmt
    $check_stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cars WHERE name = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $car_name);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $car_data = mysqli_fetch_assoc($check_result);
    mysqli_stmt_close($check_stmt);
    
    if ($car_data) {
        $current = $car_data['quantity'];
        $new_qty = max(0, $current + $delta);
        
        // Update cars
        $update_stmt = mysqli_prepare($conn, "UPDATE cars SET quantity = ? WHERE name = ?");
        mysqli_stmt_bind_param($update_stmt, "is", $new_qty, $car_name);
        $update_ok = mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
        
        if ($update_ok) {
            // Log to stock_log
            $admin_stmt = mysqli_prepare($conn, "SELECT id FROM admins WHERE username = ?");
            mysqli_stmt_bind_param($admin_stmt, "s", $_SESSION['username']);
            mysqli_stmt_execute($admin_stmt);
            $admin_result = mysqli_stmt_get_result($admin_stmt);
            $admin_data = mysqli_fetch_assoc($admin_result);
            $admin_id = $admin_data ? $admin_data['id'] : NULL;
            mysqli_stmt_close($admin_stmt);

            $log_stmt = mysqli_prepare($conn, "INSERT INTO stock_log (car_name, old_quantity, new_quantity, delta, admin_id, reason) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($log_stmt, "siiiis", $car_name, $current, $new_qty, $delta, $admin_id, $reason);
            $log_ok = mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
            if (!$log_ok) {
                $error = "Update success, but log failed.";
            }

            $action = $delta > 0 ? "added $delta" : "reduced by " . abs($delta);
            $message = "Stock $action. New quantity: $new_qty";

        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    } else {
        $error = "Car not found.";
    }
}

$total_stmt = mysqli_prepare($conn, "SELECT IFNULL(SUM(quantity), 0) as total_available FROM cars");
mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_data = mysqli_fetch_assoc($total_result);
$total_cars = (int)($total_data['total_available'] ?? 0);
mysqli_stmt_close($total_stmt);

$cars_stmt = mysqli_prepare($conn, "SELECT name, quantity FROM cars ORDER BY quantity ASC");
mysqli_stmt_execute($cars_stmt);
$cars_result = mysqli_stmt_get_result($cars_stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Management - Elite Drive Admin</title>
    <link rel="stylesheet" href="style.css?v=20260301">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0a0e17 0%, #1a1f2e 100%); }
        .stock-form { 
            background: rgba(255,255,255,0.08); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 16px; 
            padding: 2.5rem; 
            max-width: 500px; 
            margin: 2rem auto; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .stock-grid { display: grid; gap: 1rem; }
        .input-group { position: relative; }
        .input-group input, .input-group select { 
            width: 100%; padding: 1rem 1rem 1rem 3rem; 
            background: rgba(0,0,0,0.5); 
            border: 1px solid rgba(255,255,255,0.3); 
            border-radius: 12px; 
            color: #f0f0f0 !important; 
            font-size: 1rem;
        }
        .input-group input::placeholder, .input-group select::placeholder { 
            color: rgba(240,240,240,0.7) !important; 
        }
        .input-group option { 
            background: #1a1f2e; 
            color: #f0f0f0 !important; 
        }
        .input-group i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #d7ae5f; }
        .delta-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1.5rem 0; }
        .btn-stock { padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.8rem; justify-content: center; }
        .btn-add { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .btn-reduce { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .btn-stock:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .current-stocks { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 1.5rem; margin-top: 2rem; }
        .stock-item { display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .stock-item:last-child { border-bottom: none; }
        .low-stock { color: #f59e0b; font-weight: 600; }
        .btn-back { background: rgba(255,255,255,0.1); color: white; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 1rem; transition: 0.3s; }
        .btn-back:hover { background: rgba(255,255,255,0.2); transform: translateX(-5px); }
        @media (max-width: 600px) { .delta-buttons { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="navbar" style="position: sticky; top: 0; z-index: 100;">
        <div class="nav-brand">ELITE DRIVE <span style="font-size: 0.8rem;">STOCK MGMT</span></div>
        <div class="nav-links">
            <a href="admin_dashboard.php">← Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="stock-form">
        <h1 style="text-align: center; margin-bottom: 1rem; color: white;">
            <i class="fas fa-warehouse"></i> Fleet Stock Adjustment
        </h1>
<p style="text-align: center; margin-bottom: 2rem; padding: 1rem; background: rgba(215, 174, 95, 0.2); border-radius: 12px; border: 1px solid #d7ae5f;">
            <strong>Total Available: <?php echo number_format($total_cars); ?> cars</strong>
            <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
                <br><small style="color: #f59e0b;">🔧 DEBUG MODE - Errors visible</small>
            <?php endif; ?>
            <?php if ($total_cars == 0): ?>
                <br><small style="color: #f59e0b;">No cars loaded! <a href="db_check.php" style="color: #10b981;">Run db_check.php to populate</a></small>
            <?php endif; ?>
        </p>
        
        <?php if (isset($debug) && $debug): ?>
            <div style="background: rgba(255,193,7,0.2); border: 1px solid #f59e0b; color: #f59e0b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
                🔧 Debug enabled. Total cars: <?php echo $total_cars; ?> (query: <?php echo json_encode($total_data); ?>). Cars table: <?php echo mysqli_num_rows($cars_result); ?> rows.
            </div>
        <?php endif; ?>
        
<?php if (isset($message) && $message): ?>
            <div style="background: rgba(16,185,129,0.2); border: 1px solid #10b981; color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
<?php if (isset($error) && $error): ?>
            <div style="background: rgba(239,68,68,0.2); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="stock-grid">
                <div class="input-group">
                    <i class="fas fa-car"></i>
                    <select name="car_name" required>
                        <option value="">Select Car...</option>
                        <?php 
                        mysqli_data_seek($cars_result, 0); // Reset if refetched
                        while($car = mysqli_fetch_assoc($cars_result)): ?>
                            <option value="<?php echo htmlspecialchars($car['name']); ?>">
                                <?php echo htmlspecialchars($car['name']); ?> (Current: <?php echo $car['quantity']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-plus"></i>
                    <input type="number" name="add_stock" placeholder="Amount to ADD" min="0" max="999" step="1" value="">
                </div>
                <div class="input-group">
                    <i class="fas fa-minus"></i>
                    <input type="number" name="reduce_stock" placeholder="Amount to REDUCE" min="0" max="999" step="1" value="">
                </div>
            </div>
            
            <div class="delta-buttons">
                <button type="submit" name="update_stock" value="1" class="btn-stock btn-add">
                    <i class="fas fa-plus-circle"></i> Add Stock
                </button>
                <button type="submit" name="update_stock" value="1" class="btn-stock btn-reduce">
                    <i class="fas fa-minus-circle"></i> Reduce Stock
                </button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="db_check.php" class="btn-back" style="background: rgba(16,185,129,0.2);">
                <i class="fas fa-database"></i> Check DB / Populate Cars
            </a>
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Clear form on success
        <?php if (isset($message) && $message): ?>
        document.querySelector('form').reset();
        <?php endif; ?>
        // Auto-focus car select
        document.querySelector('select[name="car_name"]').focus();
    </script>
</body>
</html>

