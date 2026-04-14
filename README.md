# Car Rental Booking Website

A complete car rental booking system built with PHP and MySQL. No images required - uses emoji icons for a clean, modern interface.

## Features

- ✅ User Registration & Login
- ✅ Browse Available Cars
- ✅ Filter by Car Type
- ✅ Sort by Price, Name, or Year
- ✅ Book Cars with Date Selection
- ✅ View and Manage Bookings
- ✅ Cancel Bookings
- ✅ Responsive Design
- ✅ No Images Required (Uses Emoji Icons)

## Setup Instructions

### 1. Prerequisites
- XAMPP (or any PHP & MySQL server)
- Web Browser

### 2. Installation Steps

1. **Copy Files**
   - Copy all files to `c:\xampp\htdocs\platform-tech\`

2. **Start XAMPP**
   - Start Apache
   - Start MySQL

3. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "Import" tab
   - Select the `Database.sql` file
   - Click "Go"
   
   This will create:
   - Database: `vanguard_db`
   - Tables: users, admins, cars, parts, accessories, bookings
   - Sample data for 10 cars
   - Admin account (username: admin, password: admin123)

4. **Access the Website**
   - Open your browser
   - Navigate to: http://localhost/platform-tech/

## Usage

### For Customers:

1. **Register an Account**
   - Click "Register" on the login page
   - Enter username and password
   - Submit to create account

2. **Login**
   - Use your credentials to login
   - You'll be redirected to the dashboard

3. **Browse Cars**
   - Click "Browse Cars" in the navigation
   - Filter by car type (Sedan, SUV, Sports, etc.)
   - Sort by price, name, or year

4. **Book a Car**
   - Click "Book Now" on any available car
   - Select pick-up and return dates
   - Confirm booking
   - Price is calculated automatically

5. **Manage Bookings**
   - Click "My Bookings" to view all your bookings
   - Cancel bookings if needed
   - View booking status and details

### For Admins:

Admin login is available but the admin panel is not implemented in this version.
- Username: admin
- Password: admin123

## File Structure

```
platform-tech/
├── index.php           - Entry point (redirects to login)
├── login.php           - Login page
├── register.php        - Registration page
├── dashboard.php       - Main dashboard
├── cars.php           - Browse cars page
├── book.php           - Car booking page
├── mybookings.php     - View bookings page
├── logout.php         - Logout handler
├── config.php         - Database configuration
├── style.css          - All CSS styles
├── Database.sql       - Database schema and sample data
├── DatabaseConnection.java - Java DB connection
├── Car.java          - Car model class
├── Part.java         - Part model class
├── Accessory.java    - Accessory model class
└── DAO files         - Data Access Objects
```

## Database Configuration

Default settings in `config.php`:
- Host: localhost
- User: root
- Password: (empty)
- Database: vanguard_db

If your MySQL has different settings, update `config.php`.

## Features Explained

### No Images Required
- All vehicles use emoji icons 🚗
- Clean, modern interface
- Fast loading times
- No need to manage image files

### Automatic Inventory Management
- When a car is booked, quantity decreases
- When booking is cancelled, quantity increases
- Only available cars are shown

### Dynamic Pricing
- Daily rates for each car
- Total price calculated based on rental duration
- Real-time price updates as dates change

### Booking Status
- Confirmed: Active booking
- Cancelled: User cancelled
- Completed: Rental period ended

## Sample Data

The database includes 10 sample cars:
- Toyota Camry - ₱2,657,000/day
- Honda Civic - ₱1,583,000/day
- Ford Mustang - ₱3,499,000/day
- Tesla Model 3 - ₱2,109,000/day
- Jeep Wrangler - ₱4,790,000/day
- BMW X5 - ₱5,990,000/day
- Mercedes-Benz C-Class - ₱3,990,000/day
- Chevrolet Suburban - ₱8,634,888/day
- Nissan Altima - ₱1,858,000/day
- Hyundai Tucson - ₱1,680,000/day

## Troubleshooting

### Database Connection Error
- Make sure MySQL is running in XAMPP
- Verify database name is `vanguard_db`
- Check `config.php` settings

### Page Not Found
- Ensure files are in correct directory: `c:\xampp\htdocs\platform-tech\`
- Check that Apache is running
- Use correct URL: http://localhost/platform-tech/

### Cannot Login After Registration
- Check if users table was created
- Verify password hashing is working
- Try registering a new account

## Security Notes

⚠️ **For Development Only**

This is a learning/demonstration project. For production use:
- Add CSRF protection
- Use prepared statements everywhere (already done in DAOs)
- Add input validation
- Implement proper session security
- Use HTTPS
- Add rate limiting
- Sanitize all user inputs

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (via XAMPP)
- **Java**: Model classes and DAO pattern

## Future Enhancements

- [ ] Admin panel for managing cars
- [ ] Payment integration
- [ ] Email notifications
- [ ] User profile management
- [ ] Car reviews and ratings
- [ ] Advanced search filters
- [ ] Booking history PDF export
- [ ] Multi-language support

## License

This project is for educational purposes.

## Support

For issues or questions, check:
1. XAMPP is running
2. Database is imported correctly
3. PHP error logs in XAMPP

---

Made with ❤️ for learning PHP & MySQL
