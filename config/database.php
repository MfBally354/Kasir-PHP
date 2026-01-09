<?php
// ===================================
// config/database.php
// Database Configuration
// ===================================

// Deteksi environment: Docker atau Local
$isDocker = getenv('DOCKER_ENV') !== false || file_exists('/.dockerenv');

if ($isDocker) {
    // ===== DOCKER ENVIRONMENT =====
    define('DB_HOST', 'db');                      // Nama service di docker-compose
    define('DB_USER', 'iqbal');                   // User Docker MySQL
    define('DB_PASS', '#semarangwhj354iqbal#');   // Password Docker MySQL
    define('DB_NAME', 'kasir_db');
} else {
    // ===== LOCAL SERVER ENVIRONMENT (php -S) =====
    define('DB_HOST', 'localhost');               // Localhost MySQL
    define('DB_USER', 'iqbal');                   // User lokal Anda
    define('DB_PASS', '#semarangwhj354iqbal#');   // Password lokal Anda
    define('DB_NAME', 'kasir_db');
}

define('DB_CHARSET', 'utf8mb4');

// Function untuk mendapatkan koneksi database
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_TIMEOUT            => 10  // Timeout 10 detik
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Log sukses (optional, untuk debugging)
        error_log("✅ Database connected: " . DB_HOST . " as " . DB_USER);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Log error dengan detail
        $errorMsg = "❌ Database Connection Error!\n";
        $errorMsg .= "Error: " . $e->getMessage() . "\n";
        $errorMsg .= "Host: " . DB_HOST . "\n";
        $errorMsg .= "User: " . DB_USER . "\n";
        $errorMsg .= "Database: " . DB_NAME . "\n";
        $errorMsg .= "Environment: " . (file_exists('/.dockerenv') ? 'Docker' : 'Local') . "\n";
        
        error_log($errorMsg);
        
        // Display user-friendly error
        die("
        <div style='font-family: Arial, sans-serif; padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
            <h2 style='color: #721c24;'>❌ Koneksi Database Gagal!</h2>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p><strong>Host:</strong> " . DB_HOST . "</p>
            <p><strong>User:</strong> " . DB_USER . "</p>
            <p><strong>Database:</strong> " . DB_NAME . "</p>
            <p><strong>Environment:</strong> " . (file_exists('/.dockerenv') ? 'Docker Container' : 'Local Server') . "</p>
            <hr>
            <h3>🔧 Langkah Troubleshooting:</h3>
            <ol>
                <li><strong>Docker:</strong> Pastikan MySQL container running: <code>docker compose ps</code></li>
                <li><strong>Docker:</strong> Cek MySQL logs: <code>docker compose logs db</code></li>
                <li><strong>Docker:</strong> Tunggu MySQL initialize (~30-60 detik pertama kali)</li>
                <li><strong>Docker:</strong> Test koneksi: <code>docker exec kasir_db mysql -uiqbal -p'#semarangwhj354iqbal#' kasir_db -e 'SELECT 1'</code></li>
                <li><strong>Local:</strong> Pastikan MySQL service running: <code>sudo systemctl status mysql</code></li>
                <li><strong>Local:</strong> Test koneksi: <code>mysql -uiqbal -p'#semarangwhj354iqbal#' kasir_db -e 'SELECT 1'</code></li>
            </ol>
        </div>
        ");
    }
}

// Test connection (uncomment untuk debugging)
// try {
//     $conn = getConnection();
//     echo "✅ Database OK!";
// } catch (Exception $e) {
//     echo "❌ Error: " . $e->getMessage();
// }
?>
