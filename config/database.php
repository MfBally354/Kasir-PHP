<?php
// ===================================
// config/database.php
// Database Configuration untuk Docker
// ===================================

// Database credentials untuk Docker
define('DB_HOST', 'db');  // Nama service di docker-compose.yml
define('DB_USER', 'iqbal');
define('DB_PASS', '');
define('DB_NAME', 'kasir_db');
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
        // Log error
        error_log("Database Connection Error: " . $e->getMessage());
        
        // Display user-friendly error
        die("Koneksi database gagal. Silakan hubungi administrator.");
    }
}

// Test connection (optional, bisa dikomentari untuk production)
// try {
//     $conn = getConnection();
//     error_log("Database connected successfully!");
// } catch (Exception $e) {
//     error_log("Database connection failed: " . $e->getMessage());
// }

?>
