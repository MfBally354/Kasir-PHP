<?php
// ===================================
// config/database.php
// Database Configuration untuk Server SSH
// ===================================

// PENTING: PHP dan MySQL ada di server yang sama
// Jadi tetap pakai localhost/127.0.0.1, BUKAN IP server!
define('DB_HOST', '127.0.0.1');                  // Atau 'localhost'
define('DB_USER', 'iqbal');                      // User MySQL Anda
define('DB_PASS', '#semarangwhj354iqbal#');      // Password Anda
define('DB_NAME', 'kasir_db');                   // Nama database
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
        error_log("Host: " . DB_HOST);
        error_log("User: " . DB_USER);
        error_log("Database: " . DB_NAME);
        
        die("❌ Koneksi database gagal!<br>" . 
            "Error: " . $e->getMessage() . "<br>" .
            "Host: " . DB_HOST . "<br>" .
            "User: " . DB_USER . "<br>" .
            "Database: " . DB_NAME . "<br><br>" .
            "<strong>Debug Info:</strong><br>" .
            "• MySQL Status: <code>sudo systemctl status mysql</code><br>" .
            "• Test MySQL: <code>mysql -u iqbal -p -h 127.0.0.1</code><br>" .
            "• Import DB: <code>mysql -u iqbal -p kasir_db < database/kasir_db.sql</code>");
    }
}
?>
