CREATE DATABASE IF NOT EXISTS vanguard_db;
USE vanguard_db;

-- Table for users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for cars
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    year INT,
    quantity INT DEFAULT 0,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(name)
);

-- Table for bookings
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
total_price DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(20) DEFAULT NULL,
status ENUM('pending', 'confirmed', 'paid', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(20) DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert admin user (role: admin, password: admin123)
INSERT IGNORE INTO users (username, password, role) VALUES 
('admin', '$2y$10$7rLS2RpyYubnUHzN90nRKecHEIn8GbjO6puxJD/9S7zGZCP6.A9yG', 'admin');

-- Sample Data for Cars
INSERT IGNORE INTO cars (name, type, year, quantity, price, description) VALUES
('Toyota Camry', 'Sedan', 2023, 5, 3500.00, 'A comfortable and reliable mid-size sedan perfect for city driving.'),
('Honda Civic', 'Sedan', 2022, 3, 3000.00, 'Sporty and fuel-efficient, a great choice for daily commutes.'),
('Ford Mustang', 'Sports', 2023, 2, 8500.00, 'An iconic American muscle car for those who love speed and style.'),
('Tesla Model 3', 'Electric', 2023, 4, 6000.00, 'A high-tech electric sedan with impressive acceleration and range.'),
('Jeep Wrangler', 'SUV', 2023, 3, 5500.00, 'A rugged off-road vehicle ready for any adventure.'),
('BMW X5', 'SUV', 2023, 2, 9000.00, 'A luxury SUV offering premium comfort and powerful performance.'),
('Mercedes-Benz C-Class', 'Luxury', 2024, 2, 5800.00, 'Elegant luxury sedan'),
('Chevrolet Suburban', 'SUV', 2023, 3, 7500.00, 'Spacious family SUV'),
('Nissan Altima', 'Sedan', 2023, 4, 2200.00, 'Mid-size sedan with smart features'),
('Hyundai Tucson', 'SUV', 2023, 4, 2800.00, 'Compact SUV with great value'),
('Porsche 911', 'Sports', 2024, 1, 12000.00, 'Ultimate sports car'),
('Range Rover', 'SUV', 2024, 2, 11000.00, 'Luxury off-road SUV'),
('Ferrari 488', 'Sports', 2023, 1, 20000.00, 'High-performance supercar'),
('Lamborghini Huracan', 'Sports', 2024, 1, 25000.00, 'Italian supercar masterpiece'),
('Audi R8', 'Sports', 2024, 1, 18000.00, 'Precision German engineering'),
('McLaren 720S', 'Sports', 2024, 1, 22000.00, 'British supercar speed demon'),
('Bentley Continental', 'Luxury', 2024, 1, 15000.00, 'Ultra luxury grand tourer'),
('Rolls-Royce Ghost', 'Luxury', 2024, 1, 30000.00, 'Pinnacle of luxury motoring'),
('Maserati Levante', 'SUV', 2024, 2, 13000.00, 'Italian luxury SUV'),
('Lexus LC 500', 'Sports', 2023, 1, 14000.00, 'Japanese grand tourer'),
('Cadillac Escalade', 'SUV', 2024, 2, 10000.00, 'American luxury full-size SUV'),
('Aston Martin Vantage', 'Sports', 2024, 1, 16000.00, 'Pure predator performance'),
('Bugatti Chiron', 'Sports', 2023, 1, 50000.00, 'The fastest, most powerful supercar'),
('Koenigsegg Jesko', 'Sports', 2024, 1, 45000.00, 'The ultimate track-focused hypercar');