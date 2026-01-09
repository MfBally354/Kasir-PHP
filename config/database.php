<?php
// ===================================
// config/database.php
// Database Configuration untuk Docker
// Menggunakan user iqbal seperti biasa
// ===================================

// Database credentials - HARUS PERSIS SAMA dengan docker-compose.yml!
define('DB_HOST', 'db');                        // Nama service MySQL di Docker
define('DB_USER', 'iqbal');                     // User MySQL Anda
define('DB_PASS', '#semarangwhj354iqbal#');     // Password Anda (HATI-HATI TYPO!)
define('DB_NAME', 'kasir_db');                  // Nama database
define('DB_CHARSET', 'utf8mb4');

// Function untuk mendapatkan koneksi database
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Log error untuk debugging
        error_log("Database Connection Error: " . $e->getMessage());
        error_log("Trying to connect with:");
        error_log("Host: " . DB_HOST);
        error_log("User: " . DB_USER);
        error_log("Database: " . DB_NAME);
        
        // Tampilkan error detail saat development (HAPUS di production!)
        die("❌ Koneksi database gagal!<br>" . 
            "Error: " . $e->getMessage() . "<br>" .
            "Host: " . DB_HOST . "<br>" .
            "User: " . DB_USER . "<br>" .
            "Database: " . DB_NAME);
    }
}
?>
