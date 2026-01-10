<?php
// ===================================
// config/database.php
// Database Configuration untuk Docker
// ===================================

// Database credentials - Sesuai docker-compose.yml
define('DB_HOST', 'db');                         // Nama service di docker-compose
define('DB_USER', 'iqbal');                      // User MySQL
define('DB_PASS', '#semarangwhj354iqbal#');      // Password
define('DB_NAME', 'kasir_db');                   // Nama database
define('DB_CHARSET', 'utf8mb4');

// Function untuk mendapatkan koneksi database
function getConnection() {
    $maxRetries = 5;
    $retryDelay = 2; // seconds
    
    for ($i = 0; $i < $maxRetries; $i++) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Log successful connection
            error_log("Database connected successfully");
            
            return $pdo;
            
        } catch (PDOException $e) {
            // Log error untuk debugging
            error_log("Database Connection Attempt " . ($i + 1) . " failed: " . $e->getMessage());
            
            // Jika ini bukan percobaan terakhir, tunggu sebentar
            if ($i < $maxRetries - 1) {
                error_log("Retrying in $retryDelay seconds...");
                sleep($retryDelay);
                continue;
            }
            
            // Percobaan terakhir gagal, tampilkan error
            $errorPage = "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Database Connection Error</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        padding: 40px; 
                        background: #f5f5f5; 
                    }
                    .error-box { 
                        background: white; 
                        padding: 30px; 
                        border-radius: 10px; 
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        max-width: 800px;
                        margin: 0 auto;
                    }
                    h1 { color: #dc3545; }
                    .info { 
                        background: #f8f9fa; 
                        padding: 15px; 
                        border-radius: 5px; 
                        margin: 20px 0;
                    }
                    code { 
                        background: #e9ecef; 
                        padding: 2px 6px; 
                        border-radius: 3px; 
                        font-family: monospace;
                    }
                    .checklist { 
                        background: #fff3cd; 
                        padding: 15px; 
                        border-radius: 5px; 
                        border-left: 4px solid #ffc107;
                    }
                </style>
            </head>
            <body>
                <div class='error-box'>
                    <h1>❌ Database Connection Failed</h1>
                    
                    <div class='info'>
                        <p><strong>Error Message:</strong></p>
                        <code>" . htmlspecialchars($e->getMessage()) . "</code>
                    </div>
                    
                    <div class='info'>
                        <p><strong>Connection Details:</strong></p>
                        <ul>
                            <li>Host: <code>" . DB_HOST . "</code></li>
                            <li>Database: <code>" . DB_NAME . "</code></li>
                            <li>User: <code>" . DB_USER . "</code></li>
                        </ul>
                    </div>
                    
                    <div class='checklist'>
                        <p><strong>🔍 Troubleshooting Checklist:</strong></p>
                        <ol>
                            <li>Pastikan semua container berjalan:
                                <br><code>docker compose ps</code>
                            </li>
                            <li>Cek logs database:
                                <br><code>docker compose logs db</code>
                            </li>
                            <li>Tunggu hingga database ready (~60 detik pertama kali):
                                <br><code>docker compose logs -f db | grep 'ready for connections'</code>
                            </li>
                            <li>Restart containers jika perlu:
                                <br><code>docker compose restart</code>
                            </li>
                            <li>Rebuild dari awal:
                                <br><code>docker compose down -v</code>
                                <br><code>docker compose up -d --build</code>
                            </li>
                        </ol>
                    </div>
                    
                    <div class='info'>
                        <p><strong>💡 Tips:</strong></p>
                        <ul>
                            <li>Database MariaDB membutuhkan waktu 30-60 detik untuk initialize pertama kali</li>
                            <li>Jika masih error setelah 2 menit, coba restart: <code>docker compose restart db</code></li>
                            <li>Cek koneksi manual: <code>docker exec -it kasir_db mysql -uiqbal -p</code></li>
                        </ul>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            die($errorPage);
        }
    }
}
?>
