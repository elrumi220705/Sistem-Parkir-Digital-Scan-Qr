# Sistem-Parkir-Digital-Scan-Qr
1. Clone the repository
git clone https://github.com/elrumi220705/Sistem-Parkir-Digital-Scan-Qr
cd Sistem-Parkir-Digital-Scan-Qr

2.Import the database
-masukan database/parkir.sql di phpMyAdmin

3.Configure database connection
Edit config/db.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'parkir');

4.Run the application
php -S localhost:8000
