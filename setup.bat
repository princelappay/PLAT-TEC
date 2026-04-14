@echo off
echo ======================================
echo Car Rental Website - Database Setup
echo ======================================
echo.

echo Step 1: Checking MySQL connection...
mysql -u root -e "SELECT 'MySQL is running!' as Status;" 2>nul

if %errorlevel% neq 0 (
    echo ERROR: Cannot connect to MySQL!
    echo Please make sure MySQL is running in XAMPP.
    pause
    exit /b 1
)

echo SUCCESS: MySQL is running!
echo.

echo Step 2: Creating database and importing tables...
mysql -u root < Database.sql

if %errorlevel% neq 0 (
    echo ERROR: Failed to import database!
    pause
    exit /b 1
)

echo SUCCESS: Database created and imported!
echo.

echo ======================================
echo Setup Complete!
echo ======================================
echo.
echo Database: vanguard_db
echo Sample Cars: 10 vehicles loaded
echo Admin Account: username: admin, password: admin123
echo.
echo Next steps:
echo 1. Open your browser
echo 2. Go to: http://localhost/platform-tech/
echo 3. Register a new account or login
echo.
echo Have fun testing the car rental website!
echo.
pause
