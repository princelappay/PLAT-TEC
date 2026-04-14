CREATE DATABASE IF NOT EXISTS vanguard_db;
USE vanguard_db;

-- Users table for customer accounts
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table for admin login
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars table for rental vehicles
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT DEFAULT 1,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    , UNIQUE(name)
);

-- Parts table
CREATE TABLE IF NOT EXISTS parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price INT NOT NULL,
    description TEXT,
    quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Accessories table
CREATE TABLE IF NOT EXISTS accessories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100) NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    year_produced VARCHAR(20),
    price DECIMAL(10,2) NOT NULL,
    quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table for car rentals
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(20) DEFAULT 'card',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications table for system alerts
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(20) DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample admin account (username: admin, password: admin123) - IGNORE if duplicate
INSERT IGNORE INTO admins (username, password) VALUES 
('admin', '$2y$10$7rLS2RpyYubnUHzN90nRKecHEIn8GbjO6puxJD/9S7zGZCP6.A9yG');

-- Insert admin into users table (role: admin, password: admin123)
INSERT IGNORE INTO users (username, password, role) VALUES 
('admin', '$2y$10$7rLS2RpyYubnUHzN90nRKecHEIn8GbjO6puxJD/9S7zGZCP6.A9yG', 'admin');

-- Insert sample cars (prices are daily rental rates in PHP)
INSERT IGNORE INTO cars (name, type, year, price, quantity, description) VALUES
('Toyota Camry', 'Sedan', 2023, 2500, 5, 'Comfortable sedan perfect for business trips'),
('Honda Civic', 'Sedan', 2023, 1800, 4, 'Reliable and fuel-efficient sedan'),
('Ford Mustang', 'Sports', 2024, 4500, 2, 'Powerful sports car for thrill seekers'),
('Tesla Model 3', 'Electric', 2024, 3500, 3, 'Modern electric vehicle with autopilot'),
('Jeep Wrangler', 'SUV', 2023, 4200, 3, 'Rugged SUV for off-road adventures'),
('BMW X5', 'SUV', 2024, 6500, 2, 'Luxury SUV with premium features'),
('Mercedes-Benz C-Class', 'Luxury', 2024, 5800, 2, 'Elegant luxury sedan'),
('Chevrolet Suburban', 'SUV', 2023, 7500, 3, 'Spacious family SUV'),
('Nissan Altima', 'Sedan', 2023, 2200, 4, 'Mid-size sedan with smart features'),
('Hyundai Tucson', 'SUV', 2023, 2800, 4, 'Compact SUV with great value'),
('Porsche 911', 'Sports', 2024, 12000, 1, 'Ultimate sports car'),
('Range Rover', 'SUV', 2024, 11000, 2, 'Luxury off-road SUV'),
('Ferrari 488', 'Sports', 2023, 20000, 1, 'High-performance supercar'),
('Lamborghini Huracan', 'Sports', 2024, 25000, 1, 'Italian supercar masterpiece'),
('Audi R8', 'Sports', 2024, 18000, 1, 'Precision German engineering'),
('McLaren 720S', 'Sports', 2024, 22000, 1, 'British supercar speed demon'),
('Bentley Continental', 'Luxury', 2024, 15000, 1, 'Ultra luxury grand tourer'),
('Rolls-Royce Ghost', 'Luxury', 2024, 30000, 1, 'Pinnacle of luxury motoring'),
('Maserati Levante', 'SUV', 2024, 13000, 2, 'Italian luxury SUV'),
('Lexus LC 500', 'Sports', 2023, 14000, 1, 'Japanese grand tourer'),
('Cadillac Escalade', 'SUV', 2024, 10000, 2, 'American luxury full-size SUV'),
('Aston Martin Vantage', 'Sports', 2024, 16000, 1, 'Pure predator performance'),
('Bugatti Chiron', 'Sports', 2023, 50000, 1, 'The fastest, most powerful supercar'),
('Koenigsegg Jesko', 'Sports', 2024, 45000, 1, 'The ultimate track-focused hypercar');

-- Insert sample parts
INSERT IGNORE INTO parts (name, category, price, description, quantity) VALUES
('Brake Pads', 'Brakes', 80, 'High-quality ceramic brake pads', 50),
('Oil Filter', 'Engine', 15, 'Premium oil filter for all engines', 100),
('Air Filter', 'Engine', 25, 'High-flow air filter', 75),
('Spark Plugs', 'Engine', 12, 'Long-life spark plugs (set of 4)', 60),
('Wiper Blades', 'Exterior', 20, 'All-weather wiper blades', 80);

-- Insert sample accessories
INSERT IGNORE INTO accessories (brand, model_name, type, year_produced, price, quantity) VALUES
('Michelin', 'Pilot Sport 4S', 'Tires', '2024', 250.00, 40),
('Garmin', 'DriveSmart 65', 'GPS', '2023', 199.99, 20),
('WeatherTech', 'FloorLiner', 'Floor Mats', '2024', 149.99, 30),
('Thule', 'Motion XT', 'Roof Box', '2023', 599.99, 10),
('Pioneer', 'DMH-W4660NEX', 'Car Stereo', '2024', 349.99, 15);

-- Stock log table for audit
CREATE TABLE IF NOT EXISTS stock_log (
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
);
