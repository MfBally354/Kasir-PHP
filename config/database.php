<?php
// ===================================
// config/database.php
// Database Configuration untuk MariaDB
// ===================================

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'iqbal');
define('DB_PASS', '#semarangwhj354iqbal#');
define('DB_NAME', 'kasir_db');
define('DB_CHARSET', 'utf8mb4');

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
        error_log("Database Connection Error: " . $e->getMessage());
        error_log("Host: " . DB_HOST);
        error_log("User: " . DB_USER);
        error_log("Database: " . DB_NAME);
        
        die("❌ Koneksi database gagal!<br>" . 
            "Error: " . $e->getMessage() . "<br>" .
            "Host: " . DB_HOST . "<br>" .
            "User: " . DB_USER . "<br>" .
            "Database: " . DB_NAME);
    }
}
?>
